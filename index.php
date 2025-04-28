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
    
    // Supprimer les tables si elles existent
    $pdo->exec("DROP TABLE IF EXISTS bilan CASCADE;");
    $pdo->exec("DROP TABLE IF EXISTS competences CASCADE;");
    $pdo->exec("DROP TABLE IF EXISTS fichier CASCADE;");
    $pdo->exec("DROP TABLE IF EXISTS utilisateurs CASCADE;");

    // SQL pour créer les tables
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
    
    // Exécuter les requêtes de création
    $pdo->exec($sql_utilisateurs);
    $pdo->exec($sql_fichier);
    $pdo->exec($sql_competences);
    $pdo->exec($sql_bilan);
    
    echo "Tables créées avec succès !<br>";

    // Redirige vers la page de connexion
    header("Location: connexion.html");
    exit;

} catch (PDOException $e) {
    die("Erreur de connexion ou exécution de la requête : " . $e->getMessage());
}
?>
