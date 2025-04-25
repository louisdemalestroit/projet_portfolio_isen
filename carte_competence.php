<?php
header("Content-Type: application/json");

try {
    $pdo = new PDO("pgsql:host=localhost;dbname=iddentite", "postgres", "root", [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
} catch (PDOException $e) {
    echo json_encode(["success" => false, "error" => "Connexion échouée : " . $e->getMessage()]);
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "GET" && isset($_GET["identifiant"])) {
    $identifiant = $_GET["identifiant"];
    
    $stmt = $pdo->prepare("SELECT id, competence FROM competences WHERE iddentifiant = :identifiant");
    $stmt->execute(["identifiant" => $identifiant]);
    
    $competences = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($competences);
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $data = json_decode(file_get_contents("php://input"), true);

    if (!isset($data["identifiant"])) {
        echo json_encode(["success" => false, "error" => "Identifiant manquant"]);
        exit;
    }

    $identifiant = trim($data["identifiant"]);

    if (isset($data["competence"])) {
        $competence = trim($data["competence"]);

        if ($competence === "") {
            echo json_encode(["success" => false, "error" => "Compétence vide"]);
            exit;
        }

        try {
            $stmt = $pdo->prepare("INSERT INTO competences (competence, iddentifiant) VALUES (:competence, :identifiant)");
            $stmt->execute(["competence" => $competence, "identifiant" => $identifiant]);

            echo json_encode(["success" => true]);
        } catch (PDOException $e) {
            echo json_encode(["success" => false, "error" => "Erreur lors de l'ajout : " . $e->getMessage()]);
        }
    }

    if (isset($data["action"]) && $data["action"] === "delete" && isset($data["id"])) {
        $id = $data["id"];

        try {
            $stmt = $pdo->prepare("DELETE FROM competences WHERE id = :id AND iddentifiant = :identifiant");
            $stmt->execute(["id" => $id, "identifiant" => $identifiant]);

            echo json_encode(["success" => true]);
        } catch (PDOException $e) {
            echo json_encode(["success" => false, "error" => "Erreur lors de la suppression : " . $e->getMessage()]);
        }
    }
    exit;
}
?>
