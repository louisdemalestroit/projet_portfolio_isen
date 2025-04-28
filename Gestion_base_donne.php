<?php
header("Content-Type: application/json");

$host = "dpg-d07jpbhr0fns738kroq0-a";  // Host Render
$port = "5432";  // Port PostgreSQL
$dbname = "iddentite";  // Base de données
$user = "iddentite_user";  // Utilisateur
$password = "dTgQCI7wlWV9JgkGqeUDJ6AdydeJA9JH";  // Mot de passe

try {
    $pdo = new PDO("pgsql:host=$host;port=$port;dbname=$dbname", $user, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);

    // Récupération des élèves
    $stmt = $pdo->query("SELECT iddentifiant, prenom, nom FROM utilisateurs");
    $eleves = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        "success" => true,
        "data" => $eleves
    ]);
} catch (PDOException $e) {
    echo json_encode([
        "success" => false,
        "message" => "Erreur de connexion : " . $e->getMessage()
    ]);
}
?>
