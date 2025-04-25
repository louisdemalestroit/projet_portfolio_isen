<?php
header("Content-Type: application/json");

$host = 'localhost';
$dbname = 'iddentite';
$username = 'postgres';
$password = 'root';

try {
    $pdo = new PDO("pgsql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $jsonInput = file_get_contents('php://input');
    $data = json_decode($jsonInput, true);

    if (!isset($data['iddentifiant'], $data['notes'], $data['commentaires'], $data['page'])) {
        echo json_encode(["success" => false, "message" => "Données incomplètes"]);
        exit();
    }

    $iddentifiant = $data['iddentifiant'];
    $notes = $data['notes'];
    $commentaires = $data['commentaires'];
    $page = $data['page'];
    $bilan = $data['bilan'];

    // Récupérer l'ID utilisateur
    $stmt = $pdo->prepare("SELECT id FROM utilisateurs WHERE iddentifiant = :iddentifiant");
    $stmt->execute([':iddentifiant' => $iddentifiant]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        echo json_encode(["success" => false, "message" => "Utilisateur non trouvé"]);
        exit();
    }
    $user_id = $user['id'];

    // Transformer la page en nom de table
    $table_name = preg_replace('/[^a-zA-Z0-9_]/', '_', basename($page, '.html'));

    if ($table_name == 'bilan'){

        if($bilan == "oui"){
            $table_name = 'simplexe'; // On enregistre les données dans la table simplexe

            // Vérifier si une ligne existe déjà pour cet utilisateur
            $checkStmt = $pdo->prepare("SELECT utilisateur_id FROM $table_name WHERE utilisateur_id = :user_id");
            $checkStmt->execute([':user_id' => $user_id]);
            $exists = $checkStmt->fetch();

            if ($exists) {
                // Mise à jour si une ligne existe déjà (seules les colonnes des commentaires sont mises à jour)
                $stmt = $pdo->prepare("
                    UPDATE $table_name 
                    SET com1prof = :com1, 
                        com2prof = :com2, 
                        com3prof = :com3, 
                        com4prof = :com4, 
                        com5prof = :com5
                    WHERE utilisateur_id = :user_id
                ");
            } else {
                // Insertion d'une nouvelle ligne si elle n'existe pas encore
                $stmt = $pdo->prepare("
                    INSERT INTO $table_name 
                    (utilisateur_id, com1prof, com2prof, com3prof, com4prof, com5prof)
                    VALUES (:user_id, :com1, :com2, :com3, :com4, :com5)
                ");
            }

            // Exécution de la requête
            $stmt->execute([ 
                ':user_id' => $user_id,
                ':com1' => $commentaires[0] ?? null,
                ':com2' => $commentaires[1] ?? null,
                ':com3' => $commentaires[2] ?? null,
                ':com4' => $commentaires[3] ?? null,
                ':com5' => $commentaires[4] ?? null
            ]);

            // Récupérer les données sauvegardées (ou mises à jour)
            $responseData = [
                'utilisateur_id' => $user_id,
                'commentaires' => $commentaires,
                'success' => true,
                'message' => 'Données enregistrées avec succès dans simplexe'
            ];


        }
        else{
        $tables = ['reflexion', 'communication', 'recul', 'resolution', 'organisation'];
            $moyennes = [];
        
            foreach ($tables as $index => $table) {
                $query = $pdo->prepare("SELECT CEIL((note1prof + note2prof + note3prof + note4prof + note5prof) / 5.0) AS moyenne FROM $table WHERE utilisateur_id = :user_id");
                $query->execute([':user_id' => $user_id]);
                $result = $query->fetch(PDO::FETCH_ASSOC);
                
                if ($result) {
                    $moyennes['note' . ($index + 1)] = $result['moyenne'];
                } else {
                    $moyennes['note' . ($index + 1)] = null;
                }
            }
    
        // Vérifier si une ligne existe déjà pour cet utilisateur
        $checkStmt = $pdo->prepare("SELECT utilisateur_id FROM simplexe WHERE utilisateur_id = :user_id");
        $checkStmt->execute([':user_id' => $user_id]);
        $exists = $checkStmt->fetch();
    
        if ($exists) {
            // Mise à jour si une ligne existe déjà (seules les colonnes des commentaires sont mises à jour)
            $stmt = $pdo->prepare("
                UPDATE $table_name 
                SET com1 = :com1, 
                    com2 = :com2, 
                    com3 = :com3, 
                    com4 = :com4, 
                    com5 = :com5
                WHERE utilisateur_id = :user_id
            ");
        } else {
            // Insertion d'une nouvelle ligne si elle n'existe pas encore
            $stmt = $pdo->prepare("
                INSERT INTO $table_name 
                (utilisateur_id, com1, com2, com3, com4, com5)
                VALUES (:user_id, :com1, :com2, :com3, :com4, :com5)
            ");
        }
    
        // Exécution de la requête
        $stmt->execute([ 
            ':user_id' => $user_id,
            ':com1' => $commentaires[0] ?? null,
            ':com2' => $commentaires[1] ?? null,
            ':com3' => $commentaires[2] ?? null,
            ':com4' => $commentaires[3] ?? null,
            ':com5' => $commentaires[4] ?? null
        ]);
    
        $data = array_merge($moyennes, $commentaires);
            // Envoie des moyennes et des commentaires dans la réponse JSON
            echo json_encode([
                "success" => true,
                "message" => "Moyennes et commentaires récupérés avec succès.",
                "data" => [$data]
                
            ]);
    }
    }
    else{

    
    // Vérifier si la table existe
    $stmt = $pdo->prepare("SELECT to_regclass(:table_name)");
    $stmt->execute([':table_name' => $table_name]);
    $table_exists = $stmt->fetchColumn();

    if (!$table_exists) {
        echo json_encode(["success" => false, "message" => "Table '$table_name' inexistante"]);
        exit();
    }
    // Vérifier si une ligne existe déjà
    $checkStmt = $pdo->prepare("SELECT utilisateur_id FROM $table_name WHERE utilisateur_id = :user_id");
    $checkStmt->execute([':user_id' => $user_id]);
    $exists = $checkStmt->fetch(); 

    if ($exists) {
        $stmt = $pdo->prepare("
            UPDATE $table_name 
            SET note1prof = :note1, com1prof = :com1, 
                note2prof = :note2, com2prof = :com2, 
                note3prof = :note3, com3prof = :com3, 
                note4prof = :note4, com4prof = :com4, 
                note5prof = :note5, com5prof = :com5
            WHERE utilisateur_id = :user_id
        ");
    } else {
        $stmt = $pdo->prepare("
            INSERT INTO $table_name (utilisateur_id, note1prof, com1prof, note2prof, com2prof, note3prof, com3prof, note4prof, com4prof, note5prof, com5prof) 
            VALUES (:user_id, :note1, :com1, :note2, :com2, :note3, :com3, :note4, :com4, :note5, :com5)
        ");
    }

    $stmt->execute([ 
        ':user_id' => $user_id,
        ':note1' => $notes[0] ?? null, ':com1' => $commentaires[0] ?? null,
        ':note2' => $notes[1] ?? null, ':com2' => $commentaires[1] ?? null,
        ':note3' => $notes[2] ?? null, ':com3' => $commentaires[2] ?? null,
        ':note4' => $notes[3] ?? null, ':com4' => $commentaires[3] ?? null,
        ':note5' => $notes[4] ?? null, ':com5' => $commentaires[4] ?? null
    ]);

    echo json_encode(["success" => true, "message" => "Données enregistrées avec succès"]);
}
} catch (PDOException $e) {
    echo json_encode(["success" => false, "message" => "Erreur serveur"]);
}
?>