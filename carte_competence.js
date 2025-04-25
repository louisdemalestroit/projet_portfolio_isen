document.addEventListener("DOMContentLoaded", function () {
    const competenceContainer = document.getElementById("competenceContainer");
    const supprimerButton = document.getElementById("supprimerCompetence");
    const competenceForm = document.getElementById("competenceForm");
    const competenceInput = document.getElementById("competence");

    const params = new URLSearchParams(window.location.search);
    let identifiant = params.get("identifiant");

    if (!identifiant) {
        alert("Identifiant manquant !");
        return;
    }

    let competenceSelectionnee = null;
    let draggedElement = null;
    let offsetX = 0, offsetY = 0;

    function chargerCompetences() {
        fetch("carte_competence.php?identifiant=" + identifiant)
            .then(response => response.json())
            .then(data => {
                competenceContainer.innerHTML = "";

                data.forEach(comp => {
                    const div = document.createElement("div");
                    div.textContent = comp.competence;
                    div.classList.add("competence");
                    div.dataset.id = comp.id;
                    div.style.position = "absolute";
                    div.style.cursor = "grab";

                    // Récupérer la position enregistrée
                    const position = JSON.parse(localStorage.getItem(`competence-${comp.id}`));
                    if (position) {
                        div.style.left = position.x + "px";
                        div.style.top = position.y + "px";
                    }

                    // Sélection d'une compétence
                    div.addEventListener("click", (e) => {
                        document.querySelectorAll(".competence").forEach(el => el.classList.remove("selected"));
                        div.classList.add("selected");
                        competenceSelectionnee = comp.id;

                        // Empêcher la propagation pour éviter la désélection immédiate
                        e.stopPropagation();
                    });

                    // Drag & Drop ultra précis
                    div.addEventListener("mousedown", (e) => {
                        draggedElement = div;
                        const rect = div.getBoundingClientRect();
                        
                        // Calcul de l'offset en tenant compte de la position de la souris
                        offsetX = e.clientX - rect.left;
                        offsetY = e.clientY - rect.top;

                        // Correction pour le décalage observé
                        offsetY -= window.scrollY; // Si la page est décalée avec le scroll, cela peut affecter le calcul

                        div.style.cursor = "grabbing";

                        // Empêche la sélection du texte en même temps
                        e.preventDefault();
                    });

                    document.addEventListener("mousemove", (e) => {
                        if (draggedElement) {
                            // Calcul des nouvelles positions X et Y de l'élément
                            let x = e.clientX - offsetX;
                            let y = e.clientY - offsetY;

                            // Garder l'élément dans la zone visible
                            x = Math.max(0, Math.min(window.innerWidth - draggedElement.offsetWidth, x));
                            y = Math.max(0, Math.min(window.innerHeight - draggedElement.offsetHeight, y));

                            // Déplacer l'élément avec les coordonnées mises à jour
                            draggedElement.style.left = `${x}px`;
                            draggedElement.style.top = `${y}px`;
                        }
                    });

                    document.addEventListener("mouseup", () => {
                        if (draggedElement) {
                            // Sauvegarde de la nouvelle position dans le localStorage
                            localStorage.setItem(`competence-${draggedElement.dataset.id}`, JSON.stringify({
                                x: parseInt(draggedElement.style.left, 10),
                                y: parseInt(draggedElement.style.top, 10)
                            }));

                            draggedElement.style.cursor = "grab";
                            draggedElement = null;
                        }
                    });

                    competenceContainer.appendChild(div);
                });
            })
            .catch(error => console.error("Erreur lors du chargement :", error));
    }

    chargerCompetences();

    competenceForm.addEventListener("submit", function (event) {
        event.preventDefault();

        const nouvelleCompetence = competenceInput.value.trim();
        if (nouvelleCompetence === "") {
            alert("Veuillez entrer une compétence.");
            return;
        }

        fetch("carte_competence.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ identifiant, competence: nouvelleCompetence })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                competenceInput.value = "";
                chargerCompetences();
            } else {
                alert("Erreur : " + data.error);
            }
        })
        .catch(error => console.error("Erreur lors de l'ajout :", error));
    });

    supprimerButton.addEventListener("click", function () {
        if (!competenceSelectionnee) {
            alert("Veuillez sélectionner une compétence à supprimer.");
            return;
        }

        if (confirm("Voulez-vous vraiment supprimer cette compétence ?")) {
            fetch("carte_competence.php", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({ action: "delete", id: competenceSelectionnee, identifiant })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    localStorage.removeItem(`competence-${competenceSelectionnee}`);
                    competenceSelectionnee = null;
                    chargerCompetences();
                } else {
                    alert("Erreur lors de la suppression : " + data.error);
                }
            })
            .catch(error => console.error("Erreur lors de la suppression :", error));
        }
    });

    // Désélectionner une compétence si on clique ailleurs
    document.addEventListener("click", (e) => {
        if (!e.target.closest(".competence")) {
            document.querySelectorAll(".competence").forEach(el => el.classList.remove("selected"));
            competenceSelectionnee = null;
        }
    });
});
