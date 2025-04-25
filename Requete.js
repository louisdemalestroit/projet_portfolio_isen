// Variable pour stocker les informations de l'élève sélectionné
let eleveSelectionne = null;

document.addEventListener('DOMContentLoaded', function() {
  // Fonction pour récupérer les paramètres de l'URL
  function getQueryParams() {
      const params = new URLSearchParams(window.location.search);
      return {
          prenom: params.get("prenom"),
          nom: params.get("nom"),
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

  // Appel AJAX pour récupérer les données des élèves
  const url = "http://localhost:8000/Gestion_base_donne.php"; // URL de l'API PHP
  ajaxRequest('GET', url, (response) => {
      if (response.status === 'success') {
          elevesData = response.data; // Stocke les données des élèves

          const eleveList = document.getElementById('eleveList'); // Conteneur des noms
          eleveList.innerHTML = ''; // Réinitialise le contenu du conteneur

          // Boucle sur les données reçues et ajoute des éléments pour chaque élève
          response.data.forEach((eleve, index) => {
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
  
  // Vérifie si le bouton existe avant d'ajouter l'événement
  if (selectionButton) {
      selectionButton.addEventListener('click', function() {
          const selectedRadio = document.querySelector('input[name="eleve"]:checked');
  
          if (selectedRadio) {
              const selectedIndex = selectedRadio.value;
              const eleve = elevesData[selectedIndex];
  
              // Construire l'URL avec les paramètres (nom, prénom)
              const url = `Carte_personalise.html?prenom=${encodeURIComponent(eleve.prenom)}&nom=${encodeURIComponent(eleve.nom)}&identifiant=${encodeURIComponent(eleve.iddentifiant)}`;
  
              // Rediriger vers la nouvelle page avec les infos de l'élève
              window.location.href = url;
          } else {
              alert('Veuillez sélectionner un élève.');
          }
      });
  } else {
      console.error("Le bouton 'Sélectionner' n'a pas été trouvé.");
  }

  // Récupérer les paramètres de l'URL pour la page "Carte_personalise.html"
  const { prenom, nom,} = getQueryParams();

  if (prenom && nom) {

    const url = `getEleveByNomPrenom.php?prenom=${prenom}&nom=${nom}`;
    // Effectuer la requête AJAX
    ajaxRequest('GET', url, (response) => {
        if (response.status === 'success') {
            const eleveInfo = document.getElementById('eleveInfo');
            const eleveData = response.data;

            // Afficher les informations
            eleveInfo.innerHTML = `
                <p><strong>Nom :</strong> ${eleveData.nom}</p>
                <p><strong>Prénom :</strong> ${eleveData.prenom}</p>
                <p><strong>Identifiant :</strong> ${eleveData.iddentifiant}</p>
                <p><strong>Mot de passe :</strong> ${eleveData.mot_de_passe}</p>
            `;
        } else {
            console.error('Erreur lors de la récupération des données:', response.message);
        }
    });
    } else {
        document.getElementById('eleveInfo').innerHTML = "<p>Aucune information disponible.</p>";
    }


});

