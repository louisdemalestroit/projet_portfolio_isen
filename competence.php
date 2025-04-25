<?php
header("Content-Type: application/json"); // Réponse JSON
$host = "localhost";
$dbname = "identite";
$user = "postgres";
$password = "root";

try {
    $conn = new PDO("pgsql:host=$host;dbname=$dbname", $user, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        // Ajouter une compétence
        $identifiant = $_POST['identifiant'] ?? null;
        $competence = $_POST['competence'] ?? null;

        if ($identifiant && $competence) {
            $sql = "INSERT INTO competences (competence, iddentifiant) VALUES (:competence, :identifiant)";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':competence', $competence);
            $stmt->bindParam(':identifiant', $identifiant);
            $stmt->execute();
            echo json_encode(["message" => "Compétence ajoutée avec succès !"]);
        } else {
            echo json_encode(["error" => "Données manquantes"]);
        }
    } elseif ($_SERVER["REQUEST_METHOD"] === "GET") {
        // Récupérer les compétences d'un élève
        $identifiant = $_GET['identifiant'] ?? null;

        if ($identifiant) {
            $sql = "SELECT competence FROM competences WHERE iddentifiant = :identifiant";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':identifiant', $identifiant);
            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode($result);
        } else {
            echo json_encode(["error" => "Identifiant manquant"]);
        }
    } elseif ($_SERVER["REQUEST_METHOD"] === "DELETE") {
        // Supprimer une compétence
        $identifiant = $_POST['identifiant'] ?? null;
        $competence = $_POST['competence'] ?? null;

        if ($identifiant && $competence) {
            $sql = "DELETE FROM competences WHERE iddentifiant = :identifiant AND competence = :competence";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':competence', $competence);
            $stmt->bindParam(':identifiant', $identifiant);
            $stmt->execute();
            echo json_encode(["message" => "Compétence supprimée avec succès !"]);
        } else {
            echo json_encode(["error" => "Données manquantes"]);
        }
    } else {
        echo json_encode(["error" => "Méthode non supportée"]);
    }
} catch (PDOException $e) {
    echo json_encode(["error" => $e->getMessage()]);
}
?>
