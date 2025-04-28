<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
header("Content-Type: application/json");

require 'vendor/autoload.php';
use Cloudinary\Configuration\Configuration;
use Cloudinary\Api\Upload\UploadApi;
use Cloudinary\Api\Exception\ApiError;

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

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

Configuration::instance([
    'cloud' => [
        'cloud_name' => 'dugr7wpma',
        'api_key'    => '851562772639814',
        'api_secret' => 'wViA7fxif3TTYPykd9bFK1C9g6Y'
    ]
]);

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (!isset($_FILES["file"]) || !isset($_POST["identifiant"])) {
        die(json_encode(["error" => "Données manquantes."]));
    }

    $identifiant = $_POST["identifiant"];
    $fileTmpPath = $_FILES["file"]["tmp_name"];
    $originalFilename = pathinfo($_FILES["file"]["name"], PATHINFO_FILENAME);
    $fileExtension = pathinfo($_FILES["file"]["name"], PATHINFO_EXTENSION);

    // Nettoyer le nom du fichier en supprimant les caractères spéciaux
    $cleanedFilename = preg_replace('/[^a-zA-Z0-9_\-\.]/', '_', $originalFilename); // Remplacer les caractères invalides par _
    $publicId = $cleanedFilename . '.' . $fileExtension; // Ajouter l'extension à la fin

    try {
        $upload = (new UploadApi())->upload($fileTmpPath, [
            "folder" => "fichiers_eleves",
            "resource_type" => "auto",
            "public_id" => $publicId, // Forcer le nom exact
            "use_filename" => true,
            "unique_filename" => false
        ]);

        $fileUrl = $upload["url"]; // url de fichier

        $stmt = $pdo->prepare("INSERT INTO fichier (iddentifiant, url) VALUES (?, ?)");
        $stmt->execute([$identifiant, $fileUrl]);

        echo json_encode(["success" => true, "fileUrl" => $fileUrl]);
    } catch (ApiError $e) {
        die(json_encode(["error" => "Erreur Cloudinary: " . $e->getMessage()])); // Gérer les erreurs
    }
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "GET" && isset($_GET["identifiant"])) {
    $stmt = $pdo->prepare("SELECT url FROM fichier WHERE iddentifiant = ?");
    $stmt->execute([$_GET["identifiant"]]);
    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "DELETE") {
    $data = json_decode(file_get_contents("php://input"), true);
    if (!isset($data["url"])) {
        die(json_encode(["error" => "URL du fichier manquante."]));
    }

    $stmt = $pdo->prepare("DELETE FROM fichier WHERE url = ?");
    $stmt->execute([$data["url"]]);

    echo json_encode(["success" => true, "message" => "Fichier supprimé"]);
    exit;
}

echo json_encode(["error" => "Requête invalide."]);
?>
