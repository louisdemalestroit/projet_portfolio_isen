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
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);

    // Vérifier la présence de l'iddentifiant dans l'URL
    if (!isset($_GET['iddentifiant']) || empty($_GET['iddentifiant'])) {
        echo json_encode(["success" => false, "error" => "Identifiant élève manquant."]);
        exit();
    }

    $iddentifiant = $_GET['iddentifiant']; // Récupération de l'identifiant

    // Vérifier si la table `bilan` existe
    $stmt = $pdo->query("SELECT to_regclass('public.bilan')");
    $tableExists = $stmt->fetchColumn();

    if (!$tableExists) {
        echo json_encode(["success" => false, "error" => "La table 'bilan' n'existe pas."]);
        exit();
    }

    // Récupérer les données du bilan pour l'élève sélectionné
    $stmt = $pdo->prepare("SELECT personnel, annalyse, description, projet FROM bilan WHERE iddentifiant = :iddentifiant");
    $stmt->execute(['iddentifiant' => $iddentifiant]);
    $bilan = $stmt->fetch();

    if (!$bilan) {
        echo json_encode(["success" => false, "error" => "Aucun bilan trouvé pour cet élève."]);
        exit();
    }

    // Retourner les données du bilan sous forme JSON
    echo json_encode(["success" => true, "bilan" => $bilan]);

} catch (PDOException $e) {
    // En cas d'erreur de connexion ou autre erreur SQL
    echo json_encode(["success" => false, "error" => "Erreur de connexion : " . $e->getMessage()]);
}
?>
