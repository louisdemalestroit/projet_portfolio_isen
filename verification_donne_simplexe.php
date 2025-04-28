<?php
header("Content-Type: application/json");

// Informations de connexion à la base de données
$host = 'localhost';
$dbname = 'iddentite';
$username = 'postgres';
$password = 'isen44';

try {
    // Connexion à la base de données
    $db = new PDO("pgsql:host=$host;dbname=$dbname", $username, $password);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Lire les données JSON envoyées dans la requête POST
    $data = json_decode(file_get_contents('php://input'), true);

    if (isset($data['page_name'], $data['iddentifiant'])) {
        // Récupérer les valeurs de la requête
        $pageName = $data['page_name'];
        $iddentifiant = $data['iddentifiant'];

        // Vérifier que l'identifiant est bien fourni
        if (empty($iddentifiant)) {
            echo json_encode(["success" => false, "message" => "Identifiant manquant"]);
            exit();
        }

        // Chercher l'id correspondant à l'identifiant dans la table utilisateurs
        $stmt = $db->prepare("SELECT id FROM utilisateurs WHERE iddentifiant = :iddentifiant");
        $stmt->execute([':iddentifiant' => $iddentifiant]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            echo json_encode(["success" => false, "message" => "Utilisateur non trouvé"]);
            exit();
        }

        // Récupération de l'ID utilisateur
        $user_id = $user['id'];

        // Supprimer l'extension .html si elle existe
        $pageNameWithoutExtension = pathinfo($pageName, PATHINFO_FILENAME);
        if ($pageNameWithoutExtension == 'bilan' || $pageNameWithoutExtension == 'bilan_prof'){
            // Calcul des moyennes pour chaque table
            $tables = ['reflexion', 'communication', 'recul', 'resolution', 'organisation'];
            $moyennes = [];
        
            foreach ($tables as $index => $table) {
                $query = $db->prepare("SELECT CEIL((note1 + note2 + note3 + note4 + note5) / 5.0) AS moyenne FROM $table WHERE utilisateur_id = :user_id");
                $query->execute([':user_id' => $user_id]);
                $result = $query->fetch(PDO::FETCH_ASSOC);
                
                if ($result) {
                    $moyennes['note' . ($index + 1)] = $result['moyenne'];
                } else {
                    $moyennes['note' . ($index + 1)] = null;
                }
            }
        
            // Récupérer les commentaires depuis la table simplexe
            $commentsQuery = $db->prepare("SELECT com1, com2, com3, com4, com5 FROM simplexe WHERE utilisateur_id = :user_id");
            $commentsQuery->execute([':user_id' => $user_id]);
            $commentsResult = $commentsQuery->fetch(PDO::FETCH_ASSOC);
        
            // Vérifie si des commentaires existent
            if ($commentsResult) {
                $comments = [
                    'com1' => $commentsResult['com1'],
                    'com2' => $commentsResult['com2'],
                    'com3' => $commentsResult['com3'],
                    'com4' => $commentsResult['com4'],
                    'com5' => $commentsResult['com5']
                ];
            } else {
                // Si aucun commentaire trouvé, les valeurs seront null
                $comments = [
                    'com1' => null,
                    'com2' => null,
                    'com3' => null,
                    'com4' => null,
                    'com5' => null
                ];
            }
            $data = array_merge($moyennes, $comments);
            // Envoie des moyennes et des commentaires dans la réponse JSON
            echo json_encode([
                "success" => true,
                "message" => "Moyennes et commentaires récupérés avec succès.",
                "data" => [$data]
                
            ]);
        }
        

        else {
            // Vérifier que la table existe avant d'exécuter la requête
            $tableCheck = $db->prepare("SELECT to_regclass(:tableName) AS table_exists");
            $tableCheck->execute([':tableName' => $pageNameWithoutExtension]);
            $tableExists = $tableCheck->fetch(PDO::FETCH_ASSOC)['table_exists'];

            if (!$tableExists) {
                echo json_encode(["success" => false, "message" => "Table introuvable"]);
                exit();
            }

            // Récupérer les données de la table avec le même nom que la page
            $query = $db->prepare("SELECT * FROM $pageNameWithoutExtension WHERE utilisateur_id = :user_id");
            $query->execute([':user_id' => $user_id]);

            $data = $query->fetchAll(PDO::FETCH_ASSOC);

            if ($data) {
                echo json_encode([
                    "success" => true,
                    "message" => "Données récupérées avec succès.",
                    "data" => $data
                ]);
            } else {
                echo json_encode(["success" => false, "message" => "Aucune donnée trouvée pour cet utilisateur."]);
            }
        }
    } else {
        echo json_encode(["success" => false, "message" => "Données incomplètes (page_name ou identifiant manquant)."]);
    }
} catch (PDOException $e) {
    echo json_encode(["success" => false, "message" => "Erreur de connexion à la base de données : " . $e->getMessage()]);
}
?>
