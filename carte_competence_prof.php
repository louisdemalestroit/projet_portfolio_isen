<?php
header("Content-Type: application/json");

try {
    $pdo = new PDO("pgsql:host=localhost;dbname=iddentite", "postgres", "root", [
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
