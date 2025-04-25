<?php
$host = 'localhost';
$db = 'iddentite';
$user = 'postgres';
$pass = 'root';

try {
    $pdo = new PDO("pgsql:host=$host;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo json_encode(['error' => 'Connexion échouée: ' . $e->getMessage()]);
    exit;
}

// 🔹 Récupération des données du bilan
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['identifiant'])) {
    $stmt = $pdo->prepare("SELECT personnel, annalyse, description, projet FROM bilan WHERE iddentifiant = :identifiant");
    $stmt->execute(['identifiant' => $_GET['identifiant']]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    echo json_encode($result ?: ['personnel' => '', 'annalyse' => '', 'description' => '', 'projet' => '']);
    exit;
}

// 🔹 Enregistrement / mise à jour du bilan
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    if (!isset($data['identifiant'], $data['personnel'], $data['annalyse'], $data['description'], $data['projet'])) {
        echo json_encode(['error' => 'Données incomplètes']);
        exit;
    }

    $identifiant = $data['identifiant'];
    $personnel = $data['personnel'];
    $annalyse = $data['annalyse'];
    $description = $data['description'];
    $projet = $data['projet'];

    try {
        // 🔹 Vérifier si l'utilisateur a déjà un bilan
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM bilan WHERE iddentifiant = :identifiant");
        $stmt->execute(['identifiant' => $identifiant]);
        $exists = $stmt->fetchColumn();

        if ($exists) {
            // Mise à jour si déjà existant
            $stmt = $pdo->prepare("UPDATE bilan SET personnel = :personnel, annalyse = :annalyse, description = :description, projet = :projet WHERE iddentifiant = :identifiant");
        } else {
            // Insertion sinon
            $stmt = $pdo->prepare("INSERT INTO bilan (iddentifiant, personnel, annalyse, description, projet) VALUES (:identifiant, :personnel, :annalyse, :description, :projet)");
        }

        $stmt->execute([
            'identifiant' => $identifiant,
            'personnel' => $personnel,
            'annalyse' => $annalyse,
            'description' => $description,
            'projet' => $projet
        ]);

        echo json_encode(['message' => 'Bilan enregistré avec succès']);
    } catch (PDOException $e) {
        echo json_encode(['error' => 'Erreur SQL : ' . $e->getMessage()]);
    }
}
?>
