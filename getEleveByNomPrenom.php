<?php
$host = "dpg-d07jpbhr0fns738kroq0-a";  // Le host de ta base de données Render
$port = "5432";  // Le port de PostgreSQL
$dbname = "iddentite";  // Le nom de la base de données
$user = "iddentite_user";  // L'utilisateur de la base de données
$password = "dTgQCI7wlWV9JgkGqeUDJ6AdydeJA9JH";  // Le mot de passe de l'utilisateur

// Vérifie que la requête est une requête GET
if ($_SERVER['REQUEST_METHOD'] === 'GET') {

    // Vérifie si les paramètres 'prenom' et 'nom' sont présents dans l'URL
    if (isset($_GET['prenom']) && isset($_GET['nom'])) {
        $prenom = $_GET['prenom'];
        $nom = $_GET['nom'];

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
            // Exécution de la requête SQL pour récupérer l'élève spécifique
            $query = 'SELECT * FROM utilisateurs WHERE prenom = :prenom AND nom = :nom';
            $stmt = $pdo->prepare($query);
            $stmt->bindParam(':prenom', $prenom);
            $stmt->bindParam(':nom', $nom);
            $stmt->execute();

            // Récupère les résultats sous forme de tableau associatif
            $data = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($data) {
                // Renvoie les données récupérées en JSON
                echo json_encode(['status' => 'success', 'data' => $data]);
            } else {
                // Aucun élève trouvé
                echo json_encode(['status' => 'error', 'message' => 'Élève non trouvé']);
            }
        } catch (PDOException $e) {
            // En cas d'échec de la requête SQL
            http_response_code(500);
            echo json_encode(['status' => 'error', 'message' => 'Erreur SQL : ' . $e->getMessage()]);
            exit;
        }
    } else {
        // Si les paramètres 'prenom' ou 'nom' ne sont pas présents
        http_response_code(400); // 400 Bad Request
        echo json_encode(['status' => 'error', 'message' => 'Paramètres manquants']);
    }

} else {
    // Renvoie une erreur si la méthode HTTP n'est pas GET
    http_response_code(405); // 405 Method Not Allowed
    echo json_encode(['status' => 'error', 'message' => 'Méthode non autorisée']);
}
