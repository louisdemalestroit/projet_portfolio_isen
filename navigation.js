// Fonction pour récupérer les paramètres de l'URL actuelle
function getQueryParams() {
    const params = new URLSearchParams(window.location.search);
    return {
        prenom: params.get('prenom'),
        nom: params.get('nom'),
        identifiant: params.get('identifiant')
    };
}

// Fonction pour ajouter les paramètres aux liens de navigation
function updateNavigationLinks() {
    const queryParams = getQueryParams();
    if (!queryParams.identifiant) {
        console.warn("Aucun identifiant trouvé dans l'URL !");
        return;
    }

    // Construire l'URL avec les paramètres récupérés
    const newQueryString = `?prenom=${encodeURIComponent(queryParams.prenom)}&nom=${encodeURIComponent(queryParams.nom)}&identifiant=${encodeURIComponent(queryParams.identifiant)}`;

    // Mettre à jour tous les liens de navigation avec ces paramètres
    document.querySelectorAll("a.nav-link").forEach(link => {
        const url = new URL(link.href, window.location.origin);
        url.search = newQueryString;
        link.href = url.href;
    });

    // Afficher le nom de l'élève dans le menu (si un élément #identifiant-affiche existe)
    const identifiantAffiche = document.getElementById("identifiant-affiche");
    if (identifiantAffiche) {
        identifiantAffiche.textContent = `Bienvenue, ${queryParams.prenom} ${queryParams.nom}`;
    }
}

// Exécuter la mise à jour des liens après le chargement du DOM
document.addEventListener("DOMContentLoaded", updateNavigationLinks);
