<?php
header("Content-Type: application/json");

$host = "dpg-d07jpbhr0fns738kroq0-a";  // Le host de ta base de données Render
$port = "5432";  // Le port de PostgreSQL
$dbname = "iddentite";  // Le nom de la base de données
$user = "iddentite_user";  // L'utilisateur de la base de données
$password = "dTgQCI7wlWV9JgkGqeUDJ6AdydeJA9JH";  // Le mot de passe de l'utilisateur

try {
    // Connexion à la base de données avec les paramètres PDO
    $pdo = new PDO("pgsql:host=$host;port=$port;dbname=$dbname", $user, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);

    // Récupérer les paramètres de la requête GET
    $prenom = $_GET["prenom"] ?? "";
    $nom = $_GET["nom"] ?? "";

    if (empty($prenom) || empty($nom)) {
        echo json_encode(["success" => false, "message" => "Prénom et nom sont requis."]);
        exit;
    }

    // Exécution de la requête SQL pour récupérer les informations de l'utilisateur
    $stmt = $pdo->prepare("SELECT iddentifiant, prenom, nom, statut FROM utilisateurs WHERE prenom = :prenom AND nom = :nom");
    $stmt->execute([":prenom" => $prenom, ":nom" => $nom]);
    
    // Récupérer les résultats sous forme de tableau associatif
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        // Renvoie les données de l'utilisateur sous forme JSON
        echo json_encode([
            "success" => true,
            "identifiant" => $user['iddentifiant'],
            "prenom" => $user['prenom'],
            "nom" => $user['nom'],
            "statut" => $user['statut']
        ]);
    } else {
        // Si aucun utilisateur trouvé
        echo json_encode(["success" => false, "message" => "Aucun utilisateur trouvé pour le prénom et nom donnés."]);
    }

} catch (PDOException $e) {
    // En cas d'erreur de connexion à la base de données
    echo json_encode(["success" => false, "message" => "Erreur de connexion à la base de données : " . $e->getMessage()]);
}
?>
