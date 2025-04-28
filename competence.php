<?php
header("Content-Type: application/json"); // Réponse JSON

$host = "dpg-d07jpbhr0fns738kroq0-a";  // Le host de ta base de données Render
$port = "5432";  // Le port de PostgreSQL
$dbname = "iddentite";  // Le nom de la base de données
$user = "iddentite_user";  // L'utilisateur de la base de données
$password = "dTgQCI7wlWV9JgkGqeUDJ6AdydeJA9JH";  // Le mot de passe de l'utilisateur

try {
    $conn = new PDO("pgsql:host=$host;port=$port;dbname=$dbname", $user, $password); // Correction ici aussi (port précisé)
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
        $input = json_decode(file_get_contents('php://input'), true);
        $identifiant = $input['identifiant'] ?? null;
        $competence = $input['competence'] ?? null;

        if ($identifiant && $competence) {
            $sql = "DELETE FROM competences WHERE iddentifiant = :identifiant AND competence = :competence";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':identifiant', $identifiant);
            $stmt->bindParam(':competence', $competence);
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

