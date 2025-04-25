function validateForm() {
    const firstName = document.getElementById("first-name").value.trim();
    const lastName = document.getElementById("last-name").value.trim();
    const password = document.getElementById("password").value.trim();
    const errorMessage = document.getElementById("error-message");

    if (!firstName || !lastName || !password) {
        errorMessage.style.display = "block";
    } else {
        errorMessage.style.display = "none";
        alert("Formulaire soumis avec succès !");
    }
}

console.log("Fichier script.js chargé !");
