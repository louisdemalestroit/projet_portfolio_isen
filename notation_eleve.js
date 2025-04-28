document.addEventListener("DOMContentLoaded", () => {
    let params = new URLSearchParams(window.location.search);
    let identifiant = params.get("identifiant"); // Récupération de l'identifiant depuis l'URL
    let page = params.get("page")

    console.log("Nom de la page:", page);
    console.log("Identifiant récupéré:", identifiant);

    let postData = JSON.stringify({ page_name: page, iddentifiant: identifiant });

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

            console.log("Données de l'utilisateur:", userData);

            // Mettre à jour les champs de note et commentaire pour chaque catégorie
            for (let i = 1; i <= 7; i++) {
                let noteElement = document.getElementById(`category-${i - 1}`);
                let commentElement = document.getElementById(`textInput-${i - 1}`);

                if (noteElement) {
                    let valeurNote = parseInt(userData[`note${i}`]); // Conversion en nombre
                    let valeurNoteProf = parseInt(userData[`note${i}prof`]); // Conversion en nombre
                    noteElement.value = valeurNoteProf; // Mise à jour du champ
                    
                    console.log(`🔹 Forçage de la mise à jour du point ${i - 1} avec la valeur ${valeurNote}`);
                    updatePoint(i - 1, i - 1, valeurNote, valeurNoteProf); // Force la mise à jour du graphique
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
