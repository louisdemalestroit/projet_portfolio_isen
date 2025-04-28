<?php
// Paramètres de connexion à la base de données
$host = "dpg-d07jpbhr0fns738kroq0-a"; 
$dbname = "iddentite"; 
$user = "iddentite_user"; 
$password = "dTgQCI7wlWV9JgkGqeUDJ6AdydeJA9JH"; 

try {
    // Connexion à la base de données PostgreSQL
    $pdo = new PDO("pgsql:host=$host;dbname=$dbname", $user, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
    
    // SQL pour créer la table "utilisateurs"
    $sql_utilisateurs = "
    CREATE TABLE IF NOT EXISTS utilisateurs (
        id SERIAL PRIMARY KEY,
        prenom VARCHAR(255) NOT NULL,
        nom VARCHAR(255) NOT NULL,
        iddentifiant VARCHAR(255) UNIQUE NOT NULL,
        mot_de_passe VARCHAR(255) NOT NULL
    );";
    
    // SQL pour créer la table "fichier"
    $sql_fichier = "
    CREATE TABLE IF NOT EXISTS fichier (
        id SERIAL PRIMARY KEY,
        URL VARCHAR(255) NOT NULL,
        date_depot TIMESTAMP,
        iddentifiant VARCHAR(255),
        FOREIGN KEY (iddentifiant) REFERENCES utilisateurs(iddentifiant) ON DELETE CASCADE
    );";

    // SQL pour créer la table "competences"
    $sql_competences = "
    CREATE TABLE IF NOT EXISTS competences (
        id SERIAL PRIMARY KEY,
        
        competence VARCHAR(255),
        iddentifiant VARCHAR(255),
        FOREIGN KEY (iddentifiant) REFERENCES utilisateurs(iddentifiant) ON DELETE CASCADE
    );";

    // SQL pour créer la table "bilan"
    $sql_bilan = "
    CREATE TABLE IF NOT EXISTS bilan (
        id SERIAL PRIMARY KEY,
       
        personnel VARCHAR(500),
        projet VARCHAR(500),
        description VARCHAR(500),
        analyse VARCHAR(500),
        iddentifiant VARCHAR(255),
        FOREIGN KEY (iddentifiant) REFERENCES utilisateurs(iddentifiant) ON DELETE CASCADE
    );";

    // Exécuter la requête pour créer la table "utilisateurs"
    $pdo->exec($sql_utilisateurs);
    echo "Table 'utilisateurs' créée avec succès !<br>";

    // Exécuter la requête pour créer la table "fichier"
    $pdo->exec($sql_fichier);
    echo "Table 'fichier' créée avec succès !<br>";

    // Exécuter la requête pour créer la table "competences"
    $pdo->exec($sql_competences);
    echo "Table 'competences' créée avec succès !<br>";

    // Exécuter la requête pour créer la table "bilan"
    $pdo->exec($sql_bilan);
    echo "Table 'bilan' créée avec succès !<br>";
    
} catch (PDOException $e) {
    die("Erreur de connexion ou exécution de la requête : " . $e->getMessage());
}
?>
