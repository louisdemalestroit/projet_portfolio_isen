document.addEventListener("DOMContentLoaded", () => {
    const voirFichiersButton = document.getElementById("voirFichiers");
    const tableContainer = document.getElementById("tableContainer");

    const urlParams = new URLSearchParams(window.location.search);
    const identifiant = urlParams.get("identifiant");

    if (!identifiant) {
        alert("Identifiant de l'élève manquant dans l'URL.");
        return;
    }

    voirFichiersButton.addEventListener("click", () => {
        fetch(`afficher_fichiers.php?identifiant=${identifiant}`)
            .then(response => response.json())
            .then(data => {
                if (data.error) {
                    alert("Erreur : " + data.error);
                    return;
                }

                // Effacer les anciens résultats
                tableContainer.innerHTML = "";

                // Création du tableau
                const table = document.createElement("table");
                table.border = "1";

                // En-tête du tableau
                const thead = document.createElement("thead");
                const headerRow = document.createElement("tr");

                ["Nom du fichier", "Date de dépôt", "Actions"].forEach(text => {
                    const th = document.createElement("th");
                    th.textContent = text;
                    headerRow.appendChild(th);
                });

                thead.appendChild(headerRow);
                table.appendChild(thead);

                // Corps du tableau
                const tbody = document.createElement("tbody");

                data.forEach(file => {
                    const row = document.createElement("tr");

                    // Nom du fichier (avec lien)
                    const fileNameCell = document.createElement("td");
                    const fileLink = document.createElement("a");
                    fileLink.href = file.url;
                    fileLink.target = "_blank";
                    fileLink.textContent = file.url.split('/').pop();
                    fileNameCell.appendChild(fileLink);
                    row.appendChild(fileNameCell);

                    // Date de dépôt (formatée à la manière souhaitée)
                    const dateCell = document.createElement("td");

                    // Convertir la date de dépôt en objet Date
                    const dateDeDepot = new Date(file.date_depot);

                    // Formater la date : Année, Mois, Jour, Heure, Minute, Seconde
                    const dateArrondie = `${dateDeDepot.getFullYear()}-${String(dateDeDepot.getMonth() + 1).padStart(2, '0')}-${String(dateDeDepot.getDate()).padStart(2, '0')} ${String(dateDeDepot.getHours()).padStart(2, '0')}:${String(dateDeDepot.getMinutes()).padStart(2, '0')}:${String(dateDeDepot.getSeconds()).padStart(2, '0')}`;

                    dateCell.textContent = dateArrondie;
                    row.appendChild(dateCell);

                    // Colonne action (supprimer)
                    const actionCell = document.createElement("td");
                    const deleteButton = document.createElement("img");
                    deleteButton.src = "poubelle.jpg";
                    deleteButton.alt = "Supprimer";
                    deleteButton.classList.add("delete-icon");
                    deleteButton.style.cursor = "pointer";
                    deleteButton.addEventListener("click", () => supprimerFichier(file.url, identifiant));
                    actionCell.appendChild(deleteButton);
                    row.appendChild(actionCell);

                    tbody.appendChild(row);
                });

                table.appendChild(tbody);
                tableContainer.appendChild(table);
            })
            .catch(error => console.error("Erreur :", error));
    });

    function supprimerFichier(fileUrl, identifiant) {
        if (!confirm("Voulez-vous vraiment supprimer ce fichier ?")) return;

        fetch("upload.php", {
            method: "DELETE",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ url: fileUrl }),
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert("Fichier supprimé !");
                voirFichiersButton.click(); // Rafraîchir la liste
            } else {
                alert("Erreur : " + data.error);
            }
        })
        .catch(error => console.error("Erreur :", error));
    }
});
