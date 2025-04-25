<?php
header("Content-Type: application/json");

// Connexion à la base de données PostgreSQL
$servername = "localhost";
$username = "postgres";
$password = "root";
$dbname = "iddentite";

try {
    $pdo = new PDO("pgsql:host=$servername;dbname=$dbname", $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);

    // Vérifier la présence de l'iddentifiant dans l'URL
    if (!isset($_GET['iddentifiant']) || empty($_GET['iddentifiant'])) {
        echo json_encode(["error" => "Identifiant élève manquant."]);
        exit();
    }

    $iddentifiant = $_GET['iddentifiant']; // Récupération de l'identifiant

    // Vérifier si la table `bilan` existe (optionnel mais utile)
    $stmt = $pdo->query("SELECT to_regclass('public.bilan')");
    $tableExists = $stmt->fetchColumn();

    if (!$tableExists) {
        echo json_encode(["error" => "La table 'bilan' n'existe pas."]);
        exit();
    }

    // Récupérer les données du bilan pour l'élève sélectionné
    $stmt = $pdo->prepare("SELECT personnel, annalyse, description, projet FROM bilan WHERE iddentifiant = :iddentifiant");
    $stmt->execute(['iddentifiant' => $iddentifiant]);
    $bilan = $stmt->fetch();

    if (!$bilan) {
        echo json_encode(["error" => "Aucun bilan trouvé pour cet élève."]);
        exit();
    }

    echo json_encode($bilan);
} catch (PDOException $e) {
    echo json_encode(["error" => "Erreur de connexion : " . $e->getMessage()]);
}
?>
