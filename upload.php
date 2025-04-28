<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
header("Content-Type: application/json");

// CORS headers
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

require 'vendor/autoload.php';
use Cloudinary\Configuration\Configuration;
use Cloudinary\Api\Upload\UploadApi;
use Cloudinary\Api\Exception\ApiError;

// 🔹 Connexion à la base de données
$host = "dpg-d07jpbhr0fns738kroq0-a";  // Host Render
$port = "5432";  // Port PostgreSQL
$dbname = "iddentite";  // Nom de la base
$user = "iddentite_user";  // Utilisateur
$password = "dTgQCI7wlWV9JgkGqeUDJ6AdydeJA9JH";  // Mot de passe

try {
    $pdo = new PDO("pgsql:host=$host;port=$port;dbname=$dbname", $user, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
} catch (PDOException $e) {
    echo json_encode(["error" => "Connexion échouée : " . $e->getMessage()]);
    exit;
}

// 🔹 Configuration Cloudinary
Configuration::instance([
    'cloud' => [
        'cloud_name' => 'dugr7wpma',
        'api_key'    => '851562772639814',
        'api_secret' => 'wViA7fxif3TTYPykd9bFK1C9g6Y'
    ]
]);

// 🔹 Gestion des requêtes
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (!isset($_FILES["file"]) || !isset($_POST["identifiant"])) {
        echo json_encode(["error" => "Données manquantes."]);
        exit;
    }

    $identifiant = $_POST["identifiant"];
    $fileTmpPath = $_FILES["file"]["tmp_name"];
    $originalFilename = pathinfo($_FILES["file"]["name"], PATHINFO_FILENAME);
    $fileExtension = pathinfo($_FILES["file"]["name"], PATHINFO_EXTENSION);

    // Nettoyage du nom de fichier
    $cleanedFilename = preg_replace('/[^a-zA-Z0-9_\-\.]/', '_', $originalFilename);
    $publicId = $cleanedFilename . '.' . $fileExtension;

    try {
        $upload = (new UploadApi())->upload($fileTmpPath, [
            "folder" => "fichiers_eleves",
            "resource_type" => "auto",
            "public_id" => $publicId,
            "use_filename" => true,
            "unique_filename" => false
        ]);

        $fileUrl = $upload["url"];

        $stmt = $pdo->prepare("INSERT INTO fichier (iddentifiant, url) VALUES (?, ?)");
        $stmt->execute([$identifiant, $fileUrl]);

        echo json_encode(["success" => true, "fileUrl" => $fileUrl]);
    } catch (ApiError $e) {
        echo json_encode(["error" => "Erreur Cloudinary: " . $e->getMessage()]);
    }
    exit;

} elseif ($_SERVER["REQUEST_METHOD"] === "GET" && isset($_GET["identifiant"])) {
    $stmt = $pdo->prepare("SELECT url FROM fichier WHERE iddentifiant = ?");
    $stmt->execute([$_GET["identifiant"]]);
    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
    exit;

} elseif ($_SERVER["REQUEST_METHOD"] === "DELETE") {
    $data = json_decode(file_get_contents("php://input"), true);

    if (!isset($data["url"])) {
        echo json_encode(["error" => "URL du fichier manquante."]);
        exit;
    }

    try {
        $stmt = $pdo->prepare("DELETE FROM fichier WHERE url = ?");
        $stmt->execute([$data["url"]]);
        echo json_encode(["success" => true, "message" => "Fichier supprimé"]);
    } catch (PDOException $e) {
        echo json_encode(["error" => "Erreur SQL: " . $e->getMessage()]);
    }
    exit;

} else {
    echo json_encode(["error" => "Requête invalide."]);
}
?>
