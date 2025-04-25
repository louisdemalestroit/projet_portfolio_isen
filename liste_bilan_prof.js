document.addEventListener("DOMContentLoaded", function () {
    // Récupérer l'identifiant de l'URL
    const urlParams = new URLSearchParams(window.location.search);
    const iddentifiantEleve = urlParams.get("identifiant"); // L'URL a toujours "identifiant"

    if (!iddentifiantEleve) {
        alert("Aucun élève sélectionné.");
        return;
    }

    // Envoyer la requête AJAX au serveur
    fetch(`liste_bilan_prof.php?iddentifiant=${encodeURIComponent(iddentifiantEleve)}`)
        .then(response => response.json())
        .then(data => {
            console.log(data);

            if (data.error) {
                alert(data.error);
                return;
            }

            // Insérer les données dans les champs existants
            document.getElementById("personnel").value = data.personnel || "";
            document.getElementById("annalyse").value = data.annalyse || "";
            document.getElementById("description").value = data.description || "";
            document.getElementById("projet").value = data.projet || "";
        })
        .catch(error => console.error("Erreur de requête :", error));
});
