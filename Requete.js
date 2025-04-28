// Variable pour stocker les informations de l'élève sélectionné
let eleveSelectionne = null;

document.addEventListener('DOMContentLoaded', function() {
    // Fonction pour récupérer les paramètres de l'URL
    function getQueryParams() {
        const params = new URLSearchParams(window.location.search);
        return {
            prenom: params.get("prenom"),
            nom: params.get("nom"),
            identifiant: params.get("identifiant"),
        };
    }

    // Variables globales
    let elevesData = []; // Stocke les données des élèves

    // Fonction pour effectuer une requête AJAX
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

    // Attention : Ici on utilise l'URL de Render et pas localhost
    const baseURL = "https://projet-portfolio-isen-test.onrender.com";  // Ton site Render

    // Appel AJAX pour récupérer les données des élèves
    ajaxRequest('GET', `${baseURL}/Gestion_base_donne.php`, (response) => {
        if (response.success) {
            elevesData = response.data || []; // Stocke les données des élèves

            const eleveList = document.getElementById('eleveList'); // Conteneur des noms
            if (!eleveList) {
                console.error("Conteneur 'eleveList' non trouvé.");
                return;
            }
            eleveList.innerHTML = ''; // Réinitialise le contenu du conteneur

            // Boucle sur les données reçues et ajoute des éléments pour chaque élève
            elevesData.forEach((eleve, index) => {
                const label = document.createElement('label');
                label.innerHTML = `
                    <input type="radio" name="eleve" value="${index}">
                    ${eleve.prenom} ${eleve.nom} ${eleve.iddentifiant}
                `;
                eleveList.appendChild(label); // Ajoute le <label> dans le conteneur
                eleveList.appendChild(document.createElement('br')); // Ajoute un saut de ligne
            });
        } else {
            console.error('Erreur dans la récupération des données :', response.message);
        }
    });

    // Sélectionner un élève et rediriger vers la page avec les informations
    const selectionButton = document.getElementById('selectionButton');

    if (selectionButton) {
        selectionButton.addEventListener('click', function() {
            const selectedRadio = document.querySelector('input[name="eleve"]:checked');

            if (selectedRadio) {
                const selectedIndex = selectedRadio.value;
                const eleve = elevesData[selectedIndex];

                // Construire l'URL avec les paramètres (nom, prénom, identifiant)
                const url = `Carte_personalise.html?prenom=${encodeURIComponent(eleve.prenom)}&nom=${encodeURIComponent(eleve.nom)}&identifiant=${encodeURIComponent(eleve.iddentifiant)}`;

                // Rediriger vers la nouvelle page
                window.location.href = url;
            } else {
                alert('Veuillez sélectionner un élève.');
            }
        });
    } else {
        console.error("Le bouton 'Sélectionner' n'a pas été trouvé.");
    }

    // Afficher les infos de l'élève sélectionné si présent dans l'URL
    const { prenom, nom, identifiant } = getQueryParams();
    const eleveInfo = document.getElementById('eleveInfo');

    if (prenom && nom && eleveInfo) {
        ajaxRequest('GET', `${baseURL}/getEleveByNomPrenom.php?prenom=${prenom}&nom=${nom}`, (response) => {
            if (response.success) {
                const eleveData = response;

                // Afficher les informations de l'élève
                eleveInfo.innerHTML = `
                    <p><strong>Nom :</strong> ${eleveData.nom}</p>
                    <p><strong>Prénom :</strong> ${eleveData.prenom}</p>
                    <p><strong>Identifiant :</strong> ${eleveData.identifiant}</p>
                    <p><strong>Statut :</strong> ${eleveData.statut}</p>
                `;
            } else {
                eleveInfo.innerHTML = `<p>Erreur lors de la récupération des données : ${response.message}</p>`;
            }
        });
    } else if (eleveInfo) {
        eleveInfo.innerHTML = "<p>Aucune information disponible.</p>";
    }
});
