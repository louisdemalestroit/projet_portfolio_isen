// Sélectionner le formulaire
const loginForm = document.getElementById('login-form1');

// Ajouter un gestionnaire d'événement pour le formulaire
loginForm.addEventListener('submit', function(event) {
    event.preventDefault(); // Empêche le rechargement de la page

    // Récupérer la valeur du bouton radio sélectionné
    const selectedRole = document.querySelector('input[name="role"]:checked').value;
    const identifiant = document.getElementById("identifiant").value;
    const motDePasse = document.getElementById("mot-de-passe").value;

    // Vérifier si les champs sont remplis
    if (!identifiant || !motDePasse) {
        alert("Veuillez remplir tous les champs.");
        return;
    }

    // Créer les données à envoyer
    const formData = new FormData();
    formData.append("identifiant", identifiant);
    formData.append("mot_de_passe", motDePasse);

    // Envoi de la requête AJAX pour vérifier la connexion
    ajaxRequest("POST", "connexion.php", (response) => {
        if (response.success) {
            // Connexion réussie, stocker les infos de l'utilisateur
            sessionStorage.setItem("prenom", response.prenom);
            sessionStorage.setItem("nom", response.nom);
            sessionStorage.setItem("identifiant", response.identifiant);
            sessionStorage.setItem("statut", response.statut); // Stocke le statut

      

            // Vérification du rôle sélectionné et du statut utilisateur
            if (selectedRole === 'etudiant') {
                if (response.statut === "admin"){
                    alert("❌ Vous n'avez pas les permissions pour accéder à cette page.");
                }
                else {
                   window.location.href = `eleve.html?prenom=${encodeURIComponent(response.prenom)}&nom=${encodeURIComponent(response.nom)}&identifiant=${encodeURIComponent(response.identifiant)}`;
                }
                
            } else if (selectedRole === 'enseignant') {
                if (response.statut === "admin") {
                    // Seuls les admins peuvent accéder à la page enseignant
                    window.location.href = `Carte_Personnel.html?identifiant=${encodeURIComponent(response.identifiant)}`;
                } else {
                    alert("❌ Vous n'avez pas les permissions pour accéder à cette page.");
                }
            }
        } else {
            // Erreur de connexion
            alert("Erreur : " + response.message);
        }
    }, formData);
});

// Fonction AJAX pour envoyer la requête
function ajaxRequest(type, url, callback, data = null) {
    let xhr = new XMLHttpRequest();
    xhr.open(type, url);

    xhr.onload = () => {
        if (xhr.status === 200 || xhr.status === 201) {
            try {
                if (xhr.responseText) {
                    let resp = JSON.parse(xhr.responseText);
                    callback(resp);
                } else {
                    console.error('Réponse vide du serveur.');
                }
            } catch (error) {
                console.error('Erreur de parsing JSON:', error);
                console.error('Réponse du serveur:', xhr.responseText);
            }
        } else {
            console.error('Erreur de réponse:', xhr.responseText);
        }
    };

    xhr.onerror = () => {
        console.error('Erreur réseau lors de la requête.');
    };

    xhr.send(data);
}
