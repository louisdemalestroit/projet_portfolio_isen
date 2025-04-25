document.addEventListener("DOMContentLoaded", () => {
    const urlParams = new URLSearchParams(window.location.search);
    const prenom = urlParams.get('prenom');
    const nom = urlParams.get('nom');
    const identifiant = urlParams.get('identifiant');

    //document.getElementById("prenom").value = prenom;
    //document.getElementById("nom").value = nom;
    //document.getElementById("identifiant").value = identifiant; 
    document.getElementById("identifiant-affiche").textContent = `Bienvenue, ${prenom} ${nom}`;

    fetchBilan(identifiant);

    ["personnel", "annalyse", "description", "projet"].forEach(champ => {
        const modifyBtn = document.getElementById(`modify-${champ}`);
        const saveBtn = document.getElementById(`save-${champ}`);
        const textarea = document.getElementById(champ);
        const charCount = document.getElementById(`charCount-${champ}`);

        modifyBtn.addEventListener("click", () => {
            textarea.removeAttribute("disabled");
            modifyBtn.style.display = "none";
            saveBtn.style.display = "block";
        });

        saveBtn.addEventListener("click", () => {
            updateBilan(identifiant);
            textarea.setAttribute("disabled", "true");
            saveBtn.style.display = "none";
            modifyBtn.style.display = "block";
        });

        textarea.addEventListener("input", () => {
            if (textarea.value.length > 500) {
                textarea.value = textarea.value.substring(0, 500); // Coupe le texte à 500 caractères
            }
            charCount.textContent = `${textarea.value.length}/500`;
        });
    });
});

function fetchBilan(identifiant) {
    fetch(`eleve.php?identifiant=${identifiant}`)
    .then(response => response.json())
    .then(data => {
        ["personnel", "annalyse", "description", "projet"].forEach(champ => {
            document.getElementById(champ).value = data[champ] || "";
            document.getElementById(`charCount-${champ}`).textContent = `${data[champ]?.length || 0}/500`;
        });
    })
    .catch(error => console.error('Erreur:', error));
}

function updateBilan(identifiant) {
    const data = {
        identifiant,
        personnel: document.getElementById("personnel").value,
        annalyse: document.getElementById("annalyse").value,
        description: document.getElementById("description").value,
        projet: document.getElementById("projet").value
    };

    fetch('eleve.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    
    .catch(error => console.error('Erreur:', error));
}
