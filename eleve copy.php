<?php 
// Connexion à la base de données
$host = 'localhost';  
$db = 'iddentite';
$user = 'postgres';
$pass = 'root';

try {
    $pdo = new PDO("pgsql:host=$host;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo 'Connexion échouée : ' . $e->getMessage();
    exit;
}

// Si c'est une requête GET
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (isset($_GET['identifiant'])) {
        $identifiant = $_GET['identifiant'];

        // Récupérer toutes les données du bilan
        $stmt = $pdo->prepare("SELECT personnel, analyse, description, projet FROM bilan WHERE iddentifiant = :identifiant");
        $stmt->execute(['identifiant' => $identifiant]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        echo json_encode($result ? $result : ['personnel' => '', 'analyse' => '', 'description' => '', 'projet' => '']);
    }
}

// Si c'est une requête POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    if (isset($data['identifiant'])) {
        $identifiant = $data['identifiant'];
        $personnel = $data['personnel'] ?? '';
        $analyse = $data['analyse'] ?? '';
        $description = $data['description'] ?? '';
        $projet = $data['projet'] ?? '';

        // Vérifier si un bilan existe déjà
        $stmt = $pdo->prepare("SELECT * FROM bilan WHERE iddentifiant = :identifiant");
        $stmt->execute(['identifiant' => $identifiant]);
        $existingBilan = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($existingBilan) {
            // Mise à jour du bilan existant
            $stmt = $pdo->prepare("UPDATE bilan SET personnel = :personnel, analyse = :analyse, description = :description, projet = :projet WHERE iddentifiant = :identifiant");
        } else {
            // Création d'un nouveau bilan
            $stmt = $pdo->prepare("INSERT INTO bilan (iddentifiant, personnel, analyse, description, projet) VALUES (:identifiant, :personnel, :analyse, :description, :projet)");
        }
        
        $stmt->execute([
            'identifiant' => $identifiant,
            'personnel' => $personnel,
            'analyse' => $analyse,
            'description' => $description,
            'projet' => $projet
        ]);

        echo json_encode(['message' => 'Bilan mis à jour']);
    }
}
?>
