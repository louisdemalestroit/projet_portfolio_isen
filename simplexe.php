<?php
header("Content-Type: application/json");

$host = 'localhost';
$dbname = 'iddentite';
$username = 'postgres';
$password = 'isen44';

try {
    // Connexion à la base de données
    $db = new PDO("pgsql:host=$host;dbname=$dbname", $username, $password);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Récupérer les données JSON envoyées par AJAX
    $jsonInput = file_get_contents('php://input');
    
    // Log des données reçues pour débogage
    error_log("Données JSON reçues : " . $jsonInput);

    // Décoder le JSON
    $data = json_decode($jsonInput, true);

    // Vérification si le décodage JSON a échoué
    if ($data === null && json_last_error() !== JSON_ERROR_NONE) {
        // Log de l'erreur de décodage
        error_log("Erreur de décodage JSON : " . json_last_error_msg());
        echo json_encode(["success" => false, "message" => "Erreur de décodage JSON"]);
        exit();
    }

    // Log des données décodées
    error_log("Données décodées : " . print_r($data, true));

    if (!isset($data['utilisateur_id'], $data['notes'], $data['commentaires'], $data['page'])) {
        echo json_encode(["success" => false, "message" => "Données incomplètes"]);
        exit();
    }

    $user_id = $data['utilisateur_id'];
    $iddentifiant = $data['iddentifiant'];
    $notes = $data['notes'];
    $commentaires = $data['commentaires'];
    $page = $data['page'];

    // Chercher l'id correspondant à l'identifiant dans la table utilisateurs
    $stmt = $db->prepare("SELECT id FROM utilisateurs WHERE iddentifiant = :iddentifiant");
    $stmt->execute([':iddentifiant' => $iddentifiant]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // Si un utilisateur est trouvé, on remplace la valeur de $user_id par l'id récupéré
    if ($user) {
        $user_id = $user['id'];
    } else {
        echo json_encode(["success" => false, "message" => "Utilisateur non trouvé"]);
        exit();
    }

    // Transformer la page en nom de table valide (en supprimant l'extension .html)
    // Si tu veux garder juste "resolution", tu peux faire ce traitement.
    $table_name = preg_replace('/[^a-zA-Z0-9_]/', '_', basename($page, '.html'));

    if ($table_name == 'bilan'){
        $table_name = 'simplexe'; // On enregistre les données dans la table simplexe
    
        // Vérifier si une ligne existe déjà pour cet utilisateur
        $checkStmt = $db->prepare("SELECT utilisateur_id FROM $table_name WHERE utilisateur_id = :user_id");
        $checkStmt->execute([':user_id' => $user_id]);
        $exists = $checkStmt->fetch();
    
        if ($exists) {
            // Mise à jour si une ligne existe déjà (seules les colonnes des commentaires sont mises à jour)
            $stmt = $db->prepare("
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
            $stmt = $db->prepare("
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
    
        // Récupérer les données sauvegardées (ou mises à jour)
        $responseData = [
            'utilisateur_id' => $user_id,
            'commentaires' => $commentaires,
            'success' => true,
            'message' => 'Données enregistrées avec succès dans simplexe'
        ];
    }
    
    else{

    // Vérifier si la table existe
$stmt = $db->prepare("SELECT to_regclass(:table_name)");
$stmt->execute([':table_name' => $table_name]);
$table_exists = $stmt->fetchColumn();

if (!$table_exists) {
    echo json_encode(["success" => false, "message" => "Table '$table_name' inexistante"]);
    exit();
}

// Vérifier si une ligne existe déjà pour cet utilisateur
$checkStmt = $db->prepare("SELECT utilisateur_id FROM $table_name WHERE utilisateur_id = :user_id");
$checkStmt->execute([':user_id' => $user_id]);
$exists = $checkStmt->fetch();

// Compter le nombre de notes
$notes_count = count($notes);

if ($notes_count === 7) {
    // 7 notes et 7 commentaires
    if ($exists) {
        $stmt = $db->prepare("
            UPDATE $table_name 
            SET note1 = :note1, com1 = :com1, 
                note2 = :note2, com2 = :com2, 
                note3 = :note3, com3 = :com3, 
                note4 = :note4, com4 = :com4, 
                note5 = :note5, com5 = :com5,
                note6 = :note6, com6 = :com6,
                note7 = :note7, com7 = :com7
            WHERE utilisateur_id = :user_id
        ");
    } else {
        $stmt = $db->prepare("
            INSERT INTO $table_name 
            (utilisateur_id, note1, com1, note2, com2, note3, com3, note4, com4, note5, com5, note6, com6, note7, com7)
            VALUES (:user_id, :note1, :com1, :note2, :com2, :note3, :com3, :note4, :com4, :note5, :com5, :note6, :com6, :note7, :com7)
        ");
    }

    $params = [
        ':user_id' => $user_id,
        ':note1' => $notes[0] ?? null, ':com1' => $commentaires[0] ?? null,
        ':note2' => $notes[1] ?? null, ':com2' => $commentaires[1] ?? null,
        ':note3' => $notes[2] ?? null, ':com3' => $commentaires[2] ?? null,
        ':note4' => $notes[3] ?? null, ':com4' => $commentaires[3] ?? null,
        ':note5' => $notes[4] ?? null, ':com5' => $commentaires[4] ?? null,
        ':note6' => $notes[5] ?? null, ':com6' => $commentaires[5] ?? null,
        ':note7' => $notes[6] ?? null, ':com7' => $commentaires[6] ?? null,
    ];

} elseif ($notes_count === 6) {
    // 6 notes et 6 commentaires
    if ($exists) {
        $stmt = $db->prepare("
            UPDATE $table_name 
            SET note1 = :note1, com1 = :com1, 
                note2 = :note2, com2 = :com2, 
                note3 = :note3, com3 = :com3, 
                note4 = :note4, com4 = :com4, 
                note5 = :note5, com5 = :com5,
                note6 = :note6, com6 = :com6
            WHERE utilisateur_id = :user_id
        ");
    } else {
        $stmt = $db->prepare("
            INSERT INTO $table_name 
            (utilisateur_id, note1, com1, note2, com2, note3, com3, note4, com4, note5, com5, note6, com6)
            VALUES (:user_id, :note1, :com1, :note2, :com2, :note3, :com3, :note4, :com4, :note5, :com5, :note6, :com6)
        ");
    }

    $params = [
        ':user_id' => $user_id,
        ':note1' => $notes[0] ?? null, ':com1' => $commentaires[0] ?? null,
        ':note2' => $notes[1] ?? null, ':com2' => $commentaires[1] ?? null,
        ':note3' => $notes[2] ?? null, ':com3' => $commentaires[2] ?? null,
        ':note4' => $notes[3] ?? null, ':com4' => $commentaires[3] ?? null,
        ':note5' => $notes[4] ?? null, ':com5' => $commentaires[4] ?? null,
        ':note6' => $notes[5] ?? null, ':com6' => $commentaires[5] ?? null,
    ];

} else {
    // Cas par défaut : 5 notes et 5 commentaires
    if ($exists) {
        $stmt = $db->prepare("
            UPDATE $table_name 
            SET note1 = :note1, com1 = :com1, 
                note2 = :note2, com2 = :com2, 
                note3 = :note3, com3 = :com3, 
                note4 = :note4, com4 = :com4, 
                note5 = :note5, com5 = :com5
            WHERE utilisateur_id = :user_id
        ");
    } else {
        $stmt = $db->prepare("
            INSERT INTO $table_name 
            (utilisateur_id, note1, com1, note2, com2, note3, com3, note4, com4, note5, com5)
            VALUES (:user_id, :note1, :com1, :note2, :com2, :note3, :com3, :note4, :com4, :note5, :com5)
        ");
    }

    $params = [
        ':user_id' => $user_id,
        ':note1' => $notes[0] ?? null, ':com1' => $commentaires[0] ?? null,
        ':note2' => $notes[1] ?? null, ':com2' => $commentaires[1] ?? null,
        ':note3' => $notes[2] ?? null, ':com3' => $commentaires[2] ?? null,
        ':note4' => $notes[3] ?? null, ':com4' => $commentaires[3] ?? null,
        ':note5' => $notes[4] ?? null, ':com5' => $commentaires[4] ?? null,
    ];
}

// Exécution de la requête
$stmt->execute($params);

// Récupérer les données sauvegardées (ou mises à jour)
$responseData = [
    'utilisateur_id' => $user_id,
    'notes' => $notes,
    'commentaires' => $commentaires,
    'success' => true,
    'message' => 'Données enregistrées avec succès'
];


    echo json_encode($responseData);
}
} catch (PDOException $e) {
    error_log("Erreur PDO : " . $e->getMessage());
    echo json_encode(["success" => false, "message" => "Erreur serveur"]);
}
?>
