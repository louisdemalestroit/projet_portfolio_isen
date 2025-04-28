<?php
header("Content-Type: application/json");
$host = "dpg-d07jpbhr0fns738kroq0-a";  // Le host de ta base de données Render
$port = "5432";  // Le port de PostgreSQL
$dbname = "iddentite";  // Le nom de la base de données
$user = "iddentite_user";  // L'utilisateur de la base de données
$password = "dTgQCI7wlWV9JgkGqeUDJ6AdydeJA9JH";  // Le mot de passe de l'utilisateur
try {
    $pdo = new PDO("pgsql:host=$host;dbname=$dbname", $user, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);

    // Récupérer les données du formulaire
    $identifiant = $_POST["identifiant"] ?? "";
    $mot_de_passe = $_POST["mot_de_passe"] ?? "";
    $selectedRole = $_POST["role"] ?? "etudiant";

    // Vérifier si l'identifiant existe
    $stmt = $pdo->prepare("SELECT * FROM utilisateurs WHERE iddentifiant = :iddentifiant");
    $stmt->execute([":iddentifiant" => $identifiant]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($mot_de_passe, $user['mot_de_passe'])) {
        $statut = $user['statut'];

        // Vérifier si l'utilisateur a le droit d'accéder au rôle sélectionné
        if ($selectedRole === "enseignant" && $statut !== "admin") {
            echo json_encode(["success" => false, "message" => "Vous n'avez pas les permissions pour accéder en tant qu'enseignant."]);
            exit;
        }

        // Connexion réussie
        echo json_encode([
            "success" => true,
            "identifiant" => $user['iddentifiant'],
            "prenom" => $user['prenom'],
            "nom" => $user['nom'],
            "statut" => $statut
        ]);
    } else {
        // Identifiant ou mot de passe incorrect
        echo json_encode(["success" => false, "message" => "Identifiant ou mot de passe incorrect."]);
    }

} catch (PDOException $e) {
    echo json_encode(["success" => false, "message" => $e->getMessage()]);
}
?>
