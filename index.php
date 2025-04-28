<?php
// Paramètres de connexion à la base de données
$host = "dpg-d07jpbhr0fns738kroq0-a"; 
$dbname = "iddentite"; 
$user = "iddentite_user"; 
$password = "dTgQCI7wlWV9JgkGqeUDJ6AdydeJA9JH"; 

try {
    $pdo = new PDO("pgsql:host=$host;dbname=$dbname", $user, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
    
    $tables = $pdo->query("SELECT table_name FROM information_schema.tables WHERE table_schema = 'public'")
                  ->fetchAll(PDO::FETCH_ASSOC);
    $table_names = array_column($tables, 'table_name');
    
    if (!in_array('utilisateurs', $table_names) || !in_array('fichier', $table_names) || !in_array('competences', $table_names) || !in_array('bilan', $table_names)) {
        $sql_utilisateurs = "
        CREATE TABLE IF NOT EXISTS utilisateurs (
            id SERIAL PRIMARY KEY,
            prenom VARCHAR(255) NOT NULL,
            nom VARCHAR(255) NOT NULL,
            iddentifiant VARCHAR(255) UNIQUE NOT NULL,
            mot_de_passe VARCHAR(255) NOT NULL,
            statut VARCHAR(50) NOT NULL
        );";
        
        $sql_fichier = "
        CREATE TABLE IF NOT EXISTS fichier (
            id SERIAL PRIMARY KEY,
            URL VARCHAR(255) NOT NULL,
            date_depot TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP,
            iddentifiant VARCHAR(255),
            FOREIGN KEY (iddentifiant) REFERENCES utilisateurs(iddentifiant) ON DELETE CASCADE
        );";
        
        $sql_competences = "
        CREATE TABLE IF NOT EXISTS competences (
            id SERIAL PRIMARY KEY,
            competence VARCHAR(255),
            iddentifiant VARCHAR(255),
            FOREIGN KEY (iddentifiant) REFERENCES utilisateurs(iddentifiant) ON DELETE CASCADE
        );";
        
        $sql_bilan = "
        CREATE TABLE IF NOT EXISTS bilan (
            id SERIAL PRIMARY KEY,
            personnel VARCHAR(500),
            projet VARCHAR(500),
            description VARCHAR(500),
            annalyse VARCHAR(500),
            iddentifiant VARCHAR(255),
            FOREIGN KEY (iddentifiant) REFERENCES utilisateurs(iddentifiant) ON DELETE CASCADE
        );";
        
        // Tables supplémentaires
        $sql_simplexe = "
        CREATE TABLE IF NOT EXISTS simplexe (
            utilisateur_id INTEGER PRIMARY KEY,
            moyenne_simplexe_1 INT,
            moyenne_simplexe_2 INT,
            moyenne_simplexe_3 INT,
            moyenne_simplexe_4 INT,
            moyenne_simplexe_5 INT,
            moyenne_simplexe_6 INT,
            moyenne_simplexe_7 INT,
            FOREIGN KEY (utilisateur_id) REFERENCES utilisateurs(id) ON DELETE CASCADE
        );";
        
        $sql_communication = "
        CREATE TABLE IF NOT EXISTS communication (
            utilisateur_id INTEGER PRIMARY KEY,
            note1 INT, com1 VARCHAR(200),
            note2 INT, com2 VARCHAR(200),
            note3 INT, com3 VARCHAR(200),
            note4 INT, com4 VARCHAR(200),
            note5 INT, com5 VARCHAR(200),
            note6 INT, com6 VARCHAR(200),
            note7 INT, com7 VARCHAR(200),
            note1prof INT, com1prof VARCHAR(200),
            note2prof INT, com2prof VARCHAR(200),
            note3prof INT, com3prof VARCHAR(200),
            note4prof INT, com4prof VARCHAR(200),
            note5prof INT, com5prof VARCHAR(200),
            note6prof INT, com6prof VARCHAR(200),
            note7prof INT, com7prof VARCHAR(200),
            FOREIGN KEY (utilisateur_id) REFERENCES simplexe(utilisateur_id) ON DELETE CASCADE
        );";
        
        $sql_resolution = "
        CREATE TABLE IF NOT EXISTS resolution (
            utilisateur_id INTEGER PRIMARY KEY,
            note1 INT, com1 VARCHAR(200),
            note2 INT, com2 VARCHAR(200),
            note3 INT, com3 VARCHAR(200),
            note4 INT, com4 VARCHAR(200),
            note5 INT, com5 VARCHAR(200),
            note6 INT, com6 VARCHAR(200),
            note7 INT, com7 VARCHAR(200),
            note1prof INT, com1prof VARCHAR(200),
            note2prof INT, com2prof VARCHAR(200),
            note3prof INT, com3prof VARCHAR(200),
            note4prof INT, com4prof VARCHAR(200),
            note5prof INT, com5prof VARCHAR(200),
            note6prof INT, com6prof VARCHAR(200),
            note7prof INT, com7prof VARCHAR(200),
            FOREIGN KEY (utilisateur_id) REFERENCES simplexe(utilisateur_id) ON DELETE CASCADE
        );";
        
        $sql_reflexion = "
        CREATE TABLE IF NOT EXISTS reflexion (
            utilisateur_id INTEGER PRIMARY KEY,
            note1 INT, com1 VARCHAR(200),
            note2 INT, com2 VARCHAR(200),
            note3 INT, com3 VARCHAR(200),
            note4 INT, com4 VARCHAR(200),
            note5 INT, com5 VARCHAR(200),
            note6 INT, com6 VARCHAR(200),
            note7 INT, com7 VARCHAR(200),
            note1prof INT, com1prof VARCHAR(200),
            note2prof INT, com2prof VARCHAR(200),
            note3prof INT, com3prof VARCHAR(200),
            note4prof INT, com4prof VARCHAR(200),
            note5prof INT, com5prof VARCHAR(200),
            note6prof INT, com6prof VARCHAR(200),
            note7prof INT, com7prof VARCHAR(200),
            FOREIGN KEY (utilisateur_id) REFERENCES simplexe(utilisateur_id) ON DELETE CASCADE
        );";
        
        $sql_recul = "
        CREATE TABLE IF NOT EXISTS recul (
            utilisateur_id INTEGER PRIMARY KEY,
            note1 INT, com1 VARCHAR(200),
            note2 INT, com2 VARCHAR(200),
            note3 INT, com3 VARCHAR(200),
            note4 INT, com4 VARCHAR(200),
            note5 INT, com5 VARCHAR(200),
            note6 INT, com6 VARCHAR(200),
            note7 INT, com7 VARCHAR(200),
            note1prof INT, com1prof VARCHAR(200),
            note2prof INT, com2prof VARCHAR(200),
            note3prof INT, com3prof VARCHAR(200),
            note4prof INT, com4prof VARCHAR(200),
            note5prof INT, com5prof VARCHAR(200),
            note6prof INT, com6prof VARCHAR(200),
            note7prof INT, com7prof VARCHAR(200),
            FOREIGN KEY (utilisateur_id) REFERENCES simplexe(utilisateur_id) ON DELETE CASCADE
        );";
        
        $sql_organisation = "
        CREATE TABLE IF NOT EXISTS organisation (
            utilisateur_id INTEGER PRIMARY KEY,
            note1 INT, com1 VARCHAR(200),
            note2 INT, com2 VARCHAR(200),
            note3 INT, com3 VARCHAR(200),
            note4 INT, com4 VARCHAR(200),
            note5 INT, com5 VARCHAR(200),
            note6 INT, com6 VARCHAR(200),
            note7 INT, com7 VARCHAR(200),
            note1prof INT, com1prof VARCHAR(200),
            note2prof INT, com2prof VARCHAR(200),
            note3prof INT, com3prof VARCHAR(200),
            note4prof INT, com4prof VARCHAR(200),
            note5prof INT, com5prof VARCHAR(200),
            note6prof INT, com6prof VARCHAR(200),
            note7prof INT, com7prof VARCHAR(200),
            FOREIGN KEY (utilisateur_id) REFERENCES simplexe(utilisateur_id) ON DELETE CASCADE
        );";
        
        // Fonction PL/pgSQL
        $sql_function = "
        CREATE OR REPLACE FUNCTION after_user_insert()
        RETURNS TRIGGER AS $$
        BEGIN
            INSERT INTO simplexe (utilisateur_id) VALUES (NEW.id);
            INSERT INTO communication (utilisateur_id) VALUES (NEW.id);
            INSERT INTO resolution (utilisateur_id) VALUES (NEW.id);
            INSERT INTO reflexion (utilisateur_id) VALUES (NEW.id);
            INSERT INTO recul (utilisateur_id) VALUES (NEW.id);
            INSERT INTO organisation (utilisateur_id) VALUES (NEW.id);
            RETURN NEW;
        END;
        $$ LANGUAGE plpgsql;
        ";

        // Créer le trigger
        $sql_trigger = "
        DROP TRIGGER IF EXISTS after_user_insert ON utilisateurs;
        CREATE TRIGGER after_user_insert
        AFTER INSERT ON utilisateurs
        FOR EACH ROW
        EXECUTE FUNCTION after_user_insert();
        ";
        
        // Exécution des requêtes SQL
        $pdo->exec($sql_utilisateurs);
        $pdo->exec($sql_fichier);
        $pdo->exec($sql_competences);
        $pdo->exec($sql_bilan);
        $pdo->exec($sql_simplexe);
        $pdo->exec($sql_communication);
        $pdo->exec($sql_resolution);
        $pdo->exec($sql_reflexion);
        $pdo->exec($sql_recul);
        $pdo->exec($sql_organisation);
        $pdo->exec($sql_function);
        $pdo->exec($sql_trigger);
    }

    // Redirection vers la page de connexion
    header("Location: connexion.html");
    exit;

} catch (PDOException $e) {
    die("Erreur de connexion ou exécution de la requête : " . $e->getMessage());
}
?>
