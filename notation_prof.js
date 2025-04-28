// Récupérer les paramètres de l'URL actuelle
function getUrlParameter(name) {
    const urlParams = new URLSearchParams(window.location.search);
    return urlParams.get(name);
}



// Gestion des onglets
const tabButtons = document.querySelectorAll(".tab-button");
const tabContents = document.querySelectorAll(".tab-content");

tabButtons.forEach(button => {
    button.addEventListener("click", () => {
        tabButtons.forEach(btn => btn.classList.remove("active"));
        button.classList.add("active");

        tabContents.forEach(content => content.classList.remove("active"));
        document.getElementById(button.dataset.target).classList.add("active");

        if (button.dataset.target === "prof") {
            // Lorsque l'onglet "Professeur" est sélectionné, afficher "Rien"
            document.getElementById("prof").innerText = "Rien";
        } else {
            // Lorsque l'onglet "Élève" est sélectionné, afficher les catégories et notes
            document.getElementById("eleve").innerText = "";
        }
    });
});





document.addEventListener("DOMContentLoaded", () => {
    const identifiant = getUrlParameter("identifiant");  // Récupérer l'identifiant
    const page = getUrlParameter("page")

    let postData = JSON.stringify({ page_name: page, iddentifiant: identifiant , bilan: 'oui'});

    fetch("Simplexe_prof.php", {
        method: "POST",
        headers: {
            "Content-Type": "application/json"
        },
        body: postData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success && data.data.length > 0) {
            let userData = data.data[0]; // Récupération des données de l'utilisateur

            console.log("Données de l'utilisateur récupérées :", userData);

            // Mettre à jour les champs de note et commentaire pour chaque catégorie
            for (let i = 1; i <= 7; i++) {
                let noteElement = document.getElementById(`category-${i - 1}`);
                let commentElement = document.getElementById(`textInput-${i - 1}`);

                if (noteElement) {
                    let valeurNoteProf = parseInt(userData[`note${i}prof`]); // Conversion en nombre
                    noteElement.value = valeurNoteProf; // Mise à jour du champ
                    
                    console.log(`🔹 Forçage de la mise à jour du point ${i - 1} avec la valeur de la note prof ${valeurNoteProf}`);
                    updatePoint(i - 1, valeurNoteProf); // Force la mise à jour du graphique
                } else {
                    console.error(`❌ Problème: Élément Note ${i} introuvable !`);
                }

                if (commentElement) {
                    commentElement.value = userData[`com${i}prof`]; // Mise à jour du commentaire
                } else {
                    console.error(`❌ Problème: Élément Commentaire ${i} introuvable !`);
                }
            }

            // Maintenant, le pentagone doit être mis à jour
            updatePentagon();

        } else {
            console.warn("Aucune donnée trouvée pour cet utilisateur.");
        }
    })
    .catch(error => console.error("Erreur lors de la récupération des données:", error));
});









// Fonction pour récupérer les catégories depuis localStorage
function getCategoriesFromLocalStorage() {
    return JSON.parse(localStorage.getItem('categories')) || [];
}

// Fonction pour récupérer les notes et les commentaires
function collectFormData() {
    let queryParams = getQueryParams(); // Récupérer les paramètres de l'URL

    let data = {
        iddentifiant: getUrlParameter("identifiant") || null, // Ajoute l'identifiant de l'URL
        notes: [],
        commentaires: [],
        page: getUrlParameter("page"), // Ajoute seulement la page d'origine
        bilan : 'oui'
    };

    // Récupérer les catégories depuis localStorage
    const categories = getCategoriesFromLocalStorage();

    // Récupérer toutes les sélections (notes) et les commentaires dans les champs
    categories.forEach((category, index) => {
        const noteElement = document.getElementById(`category-${index}`);
        const commentElement = document.getElementById(`textInput-${index}`);

        if (noteElement && commentElement) {
            const note = parseInt(noteElement.value, 10); // Assurez-vous que note est un nombre
            const commentaire = commentElement.value;

            if (note && commentaire) {
                data.notes.push(note);
                data.commentaires.push(commentaire);
            } else {
                console.error(`Erreur : Les éléments pour la catégorie ${category} sont introuvables.`);
            }
        } else {
            console.error(`Erreur : Les éléments pour la catégorie ${category} sont introuvables.`);
        }
    });

    // Log de l'objet data avant de l'envoyer
    console.log("Données envoyées :", JSON.stringify(data));

    return data;
}

// Ajouter l'événement de clic sur le bouton Valider
document.getElementById('validateButton').addEventListener('click', () => {
    const data = collectFormData();

    // Appeler la fonction AJAX pour envoyer les données au serveur
    ajaxRequest('POST', 'notation_prof.php', handleResponse, JSON.stringify(data));
});

// Fonction de gestion de la réponse serveur
function handleResponse(response) {
    if (response.success) {
        alert('Données enregistrées avec succès!');
    } else {
        alert('Erreur lors de l\'enregistrement des données.');
    }
}



function handleResponse(response) {
    if (response.success) {
        alert('Données enregistrées avec succès!');
        
        // Afficher les informations reçues
        console.log('Utilisateur ID:', response.utilisateur_id);
        console.log('Notes:', response.notes);
        console.log('Commentaires:', response.commentaires);
    } else {
        alert('Erreur lors de l\'enregistrement des données.');
    }
}



// Fonction AJAX
function ajaxRequest(type, url, callback, data = null) {
    let xhr = new XMLHttpRequest();
    xhr.open(type, url);

    xhr.onload = () => {
        if (xhr.status === 200 || xhr.status === 201) {
            try {
                // Vérifiez si la réponse semble être du JSON avant de la parser
                if (xhr.responseText.trim().startsWith('{') || xhr.responseText.trim().startsWith('[')) {
                    let resp = JSON.parse(xhr.responseText);
                    callback(resp);
                } else {
                    console.error('Réponse non-JSON du serveur:', xhr.responseText);
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

    xhr.setRequestHeader('Content-Type', 'application/json');
    xhr.send(data);
} 



