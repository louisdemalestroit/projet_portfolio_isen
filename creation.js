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

document.addEventListener("DOMContentLoaded", () => {
    const statutRadios = document.querySelectorAll("input[name='statut']");
    const masterPasswordGroup = document.getElementById("master-password-group");
    
    statutRadios.forEach(radio => {
        radio.addEventListener("change", () => {
            if (document.getElementById("admin").checked) {
                masterPasswordGroup.style.display = "block";
            } else {
                masterPasswordGroup.style.display = "none";
            }
        });
    });

    document.getElementById("creation-Form2").addEventListener("submit", function (event) {
        event.preventDefault();

        let formData = new FormData(this);

        ajaxRequest("POST", "creation.php", (response) => {
            if (response.success) {
                
                window.location.href = "connexion.html";
            } else {
                alert("Erreur : " + response.message);
            }
        }, formData);
    });
});