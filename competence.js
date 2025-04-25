document.addEventListener("DOMContentLoaded", () => {
    function getQueryParams() {
        const params = new URLSearchParams(window.location.search);
        return {
            prenom: params.get("prenom"),
            nom: params.get("nom"),
            identifiant: params.get("identifiant"),
        };
    }

    const { identifiant } = getQueryParams();
    if (!identifiant) {
        alert("Aucun identifiant trouvé !");
        return;
    }
    document.getElementById("identifiant").value = identifiant;

    const form = document.getElementById("competenceForm");
    const container = document.getElementById("competenceContainer");

    form.addEventListener("submit", (event) => {
        event.preventDefault();

        const competence = document.getElementById("competence").value;

        fetch("competences.php", {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: new URLSearchParams({ identifiant, competence }),
        })
        .then(response => response.json())
        .then(() => {
            loadCompetences();
            document.getElementById("competence").value = "";
        });
    });

    function loadCompetences() {
        fetch(`competences.php?identifiant=${identifiant}`)
        .then(response => response.json())
        .then(data => {
            container.innerHTML = "";
            data.forEach(item => {
                const div = document.createElement("div");
                div.classList.add("competence");
                div.textContent = item.competence;
                div.draggable = true;
                container.appendChild(div);
            });
        });
    }

    loadCompetences();
});
