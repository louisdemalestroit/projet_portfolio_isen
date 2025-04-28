<?php
header("Content-Type: application/json");
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
    echo json_encode(["success" => false, "error" => "Connexion à la base de données échouée : " . $e->getMessage()]);
    exit;
}

// 🔹 Gestion des requêtes GET (récupération des compétences)
if ($_SERVER["REQUEST_METHOD"] === "GET" && isset($_GET["identifiant"])) {
    $identifiant = $_GET["identifiant"];
    
    $stmt = $pdo->prepare("SELECT id, competence FROM competences WHERE iddentifiant = :identifiant");
    $stmt->execute(["identifiant" => $identifiant]);
    
    $competences = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($competences);
    exit;
}
