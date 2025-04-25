<?php
try {
    $pdo = new PDO("pgsql:host=127.0.0.1;port=5432;dbname=nom_de_la_base", "utilisateur", "mot_de_passe");
    echo "Connexion réussie !";
} catch (PDOException $e) {
    echo "Erreur : " . $e->getMessage();
}
?>
