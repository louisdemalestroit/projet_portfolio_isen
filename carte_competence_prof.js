// carte_competence.js
document.addEventListener("DOMContentLoaded", function () {
    const identifiantSpan = document.getElementById("identifiant-affiche");

    const params = new URLSearchParams(window.location.search);
    let identifiant = params.get("identifiant");

    if (!identifiant) {
        alert("Identifiant manquant !");
        return;
    }

    identifiantSpan.textContent = `Identifiant : ${identifiant}`;

    function chargerCompetences() {
        fetch("carte_competence_prof.php?identifiant=" + identifiant)
            .then(response => response.json())
            .then(data => {
                document.querySelectorAll(".competence").forEach(el => el.remove());

                data.forEach(comp => {
                    const div = document.createElement("div");
                    div.textContent = comp.competence;
                    div.classList.add("competence");
                    div.dataset.id = comp.id;
                    div.style.position = "absolute";

                    // 📌 Récupération de la position sauvegardée
                    const position = JSON.parse(localStorage.getItem(`competence-${comp.id}`));
                    if (position) {
                        div.style.left = position.x + "px";
                        div.style.top = position.y + "px";
                    }

                    document.body.appendChild(div);
                });
            })
            .catch(error => console.error("Erreur lors du chargement :", error));
    }

    chargerCompetences();
});
