<?php
$host = 'localhost';
$dbname = 'iddentite';
$username = 'postgres';
$password = 'root';

// Vérifie que la requête est une requête GET
if ($_SERVER['REQUEST_METHOD'] === 'GET') {

    try {
        // Connexion à la base de données
        $pdo = new PDO("pgsql:host=$host;dbname=$dbname", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
        // En cas d'échec de connexion
        http_response_code(500); // Définit le code de réponse HTTP à 500
        echo json_encode(['status' => 'error', 'message' => 'Connexion échouée : ' . $e->getMessage()]);
        exit;
    }

    try {
        // Exécution de la requête SQL
        $query = 'SELECT nom, prenom, iddentifiant FROM utilisateurs';
        $data = $pdo->query($query)->fetchAll(PDO::FETCH_ASSOC);

        // Renvoie les données récupérées en JSON
        echo json_encode(['status' => 'success', 'data' => $data]);
    } catch (PDOException $e) {
        // En cas d'échec de la requête SQL
        http_response_code(500);
        echo json_encode(['status' => 'error', 'message' => 'Erreur SQL : ' . $e->getMessage()]);
        exit;
    }
} else {
    // Renvoie une erreur si la méthode HTTP n'est pas GET
    http_response_code(405); // 405 Method Not Allowed
    echo json_encode(['status' => 'error', 'message' => 'Méthode non autorisée']);
}
