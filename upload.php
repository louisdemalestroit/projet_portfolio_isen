<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
header("Content-Type: application/json");

// CORS headers
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// --- Étape 1 : Vérifier si Composer est installé et exécuter l'installation des dépendances si nécessaire ---
if (!file_exists(__DIR__ . '/vendor/autoload.php')) {
    die(json_encode(["error" => "Dépendances manquantes. Exécutez Composer pour installer les dépendances."]));
}

// Charger l'autoloader de Composer
require_once __DIR__ . '/vendor/autoload.php';

// --- Étape 2 : Vérification de Cloudinary --- 
use Cloudinary\Configuration\Configuration;
use Cloudinary\Api\Upload\UploadApi;
use Cloudinary\Api\Exception\ApiError;

// Configuration de Cloudinary
Configuration::instance([
    'cloud' => [
        'cloud_name' => 'dugr7wpma',
        'api_key'    => '851562772639814',
        'api_secret' => 'wViA7fxif3TTYPykd9bFK1C9g6Y'
    ]
]);

// --- Étape 3 : Connexion à PostgreSQL --- 
$host = "dpg-d07jpbhr0fns738kroq0-a";  // Le host de ta base de données Render
$port = "5432";  // Le port de PostgreSQL
$dbname = "iddentite";  // Le nom de la base de données
$user = "iddentite_user";  // L'utilisateur de la base de données
$password = "dTgQCI7wlWV9JgkGqeUDJ6AdydeJA9JH";  // Le mot de passe de l'utilisateur

try {
    $pdo = new PDO("pgsql:host=$host;dbname=$dbname", $user, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
} catch (PDOException $e) {
    die(json_encode(["error" => "Connexion échouée : " . $e->getMessage()]));
}

// --- Étape 4 : Traitement de la requête POST pour l'upload de fichier ---
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (!isset($_FILES["file"]) || !isset($_POST["identifiant"])) {
        die(json_encode(["error" => "Données manquantes."]));
    }

    $identifiant = $_POST["identifiant"];
    $fileTmpPath = $_FILES["file"]["tmp_name"];
    $originalFilename = pathinfo($_FILES["file"]["name"], PATHINFO_FILENAME);
    $fileExtension = pathinfo($_FILES["file"]["name"], PATHINFO_EXTENSION);

    // Nettoyer le nom de fichier
    $cleanedFilename = preg_replace('/[^a-zA-Z0-9_\-\.]/', '_', $originalFilename);
    $publicId = $cleanedFilename . '.' . $fileExtension;

    try {
        // Téléchargement du fichier sur Cloudinary
        $upload = (new UploadApi())->upload($fileTmpPath, [
            "folder" => "fichiers_eleves",
            "resource_type" => "auto",
            "public_id" => $publicId,
            "use_filename" => true,
            "unique_filename" => false
        ]);

        $fileUrl = $upload["url"];

        // Sauvegarde de l'URL du fichier dans la base de données
        $stmt = $pdo->prepare("INSERT INTO fichier (iddentifiant, url) VALUES (?, ?)");
        $stmt->execute([$identifiant, $fileUrl]);

        echo json_encode(["success" => true, "fileUrl" => $fileUrl]);
    } catch (ApiError $e) {
        die(json_encode(["error" => "Erreur Cloudinary: " . $e->getMessage()]));
    }
    exit;
}

// --- Étape 5 : Traitement de la requête GET pour récupérer un fichier ---
if ($_SERVER["REQUEST_METHOD"] === "GET" && isset($_GET["identifiant"])) {
    $stmt = $pdo->prepare("SELECT url FROM fichier WHERE iddentifiant = ?");
    $stmt->execute([$_GET["identifiant"]]);
    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
    exit;
}

// --- Étape 6 : Traitement de la requête DELETE pour supprimer un fichier ---
if ($_SERVER["REQUEST_METHOD"] === "DELETE") {
    $data = json_decode(file_get_contents("php://input"), true);

    if (!isset($data["url"])) {
        die(json_encode(["error" => "URL du fichier manquante."]));
    }

    try {
        // Suppression du fichier de la base de données
        $stmt = $pdo->prepare("DELETE FROM fichier WHERE url = ?");
        $stmt->execute([$data["url"]]);
        echo json_encode(["success" => true, "message" => "Fichier supprimé"]);
    } catch (PDOException $e) {
        echo json_encode(["error" => "Erreur SQL: " . $e->getMessage()]);
    }
    exit;
}

// Si aucune des conditions ci-dessus n'est remplie, renvoyer une erreur 400
echo json_encode(["error" => "Requête invalide."]);
?>
