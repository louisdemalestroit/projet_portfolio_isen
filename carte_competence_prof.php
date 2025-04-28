<?php
header("Content-Type: application/json");

// Définition des variables de connexion à la base de données
$host = "dpg-d07jpbhr0fns738kroq0-a";  // Le host de ta base de données Render
$port = "5432";  // Le port de PostgreSQL
$dbname = "iddentite";  // Le nom de la base de données
$user = "iddentite_user";  // L'utilisateur de la base de données
$password = "dTgQCI7wlWV9JgkGqeUDJ6AdydeJA9JH";  // Le mot de passe de l'utilisateur

try {
    // Connexion à la base de données PostgreSQL
    $pdo = new PDO("pgsql:host=$host;dbname=$dbname;port=$port", $user, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
} catch (PDOException $e) {
    // En cas d'échec de la connexion à la base de données
    echo json_encode(["success" => false, "error" => "Connexion à la base de données échouée : " . $e->getMessage()]);
    exit;
}

// 🔹 Gestion des requêtes GET (récupération des compétences)
if ($_SERVER["REQUEST_METHOD"] === "GET" && isset($_GET["identifiant"])) {
    $identifiant = $_GET["identifiant"];
    
    try {
        // Préparation et exécution de la requête SQL pour récupérer les compétences
        $stmt = $pdo->prepare("SELECT id, competence FROM competences WHERE iddentifiant = :identifiant");
        $stmt->execute(["identifiant" => $identifiant]);
        
        // Récupération des résultats
        $competences = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Retour des compétences en format JSON
        echo json_encode(["success" => true, "competences" => $competences]);
    } catch (PDOException $e) {
        // En cas d'erreur dans l'exécution de la requête
        echo json_encode(["success" => false, "error" => "Erreur lors de l'exécution de la requête : " . $e->getMessage()]);
    }
    exit;
} else {
    // Si la méthode HTTP n'est pas GET ou l'identifiant n'est pas fourni
    echo json_encode(["success" => false, "error" => "Méthode non autorisée ou identifiant manquant"]);
    exit;
}
?>
