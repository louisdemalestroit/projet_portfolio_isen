<?php
header("Content-Type: application/json");

$host = "localhost";
$dbname = "iddentite";
$user = "postgres";
$password = "root";

$masterPassword = "chaos controle"; // Master password défini

try {
    $pdo = new PDO("pgsql:host=$host;dbname=$dbname", $user, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);

    $prenom = $_POST["prenom"] ?? "";
    $nom = $_POST["nom"] ?? "";
    $iddentifiant = $_POST["iddentifiant"] ?? "";
    $mot_de_passe = $_POST["mot_de_passe"] ?? "";
    $statut = $_POST["statut"] ?? "etudiant";
    $inputMasterPassword = $_POST["master_password"] ?? "";

    // Vérifier si l'identifiant existe déjà
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM utilisateurs WHERE iddentifiant = :iddentifiant");
    $stmt->execute([":iddentifiant" => $iddentifiant]);
    if ($stmt->fetchColumn() > 0) {
        echo json_encode(["success" => false, "message" => "Identifiant déjà utilisé"]);
        exit;
    }

    // Vérifier le Master Password si l'utilisateur veut être admin
    if ($statut === "admin") {
        if ($inputMasterPassword !== $masterPassword) {
            echo json_encode(["success" => false, "message" => "Master Password incorrect"]);
            exit;
        }
    } else {
        $statut = "etudiant"; // Assurer que l'utilisateur a le bon statut
    }

    // Hacher le mot de passe
    $hashed_password = password_hash($mot_de_passe, PASSWORD_BCRYPT);

    // Insérer dans la base de données
    $stmt = $pdo->prepare("INSERT INTO utilisateurs (prenom, nom, iddentifiant, mot_de_passe, statut) VALUES (:prenom, :nom, :iddentifiant, :mot_de_passe, :statut)");
    $stmt->execute([
        ":prenom" => $prenom,
        ":nom" => $nom,
        ":iddentifiant" => $iddentifiant,
        ":mot_de_passe" => $hashed_password,
        ":statut" => $statut
    ]);

    echo json_encode(["success" => true]);
} catch (PDOException $e) {
    echo json_encode(["success" => false, "message" => $e->getMessage()]);
}
?>
