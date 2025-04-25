document.addEventListener("DOMContentLoaded", () => {
    const fileInput = document.getElementById("fileInput");
    const uploadForm = document.getElementById("uploadForm");
    const uploadedFilesList = document.getElementById("uploadedFilesList");
    const fileNameDisplay = document.getElementById("fileNameDisplay");

    const urlParams = new URLSearchParams(window.location.search);
    const identifiant = urlParams.get("identifiant");

    if (!identifiant) {
        alert("Identifiant manquant dans l'URL.");
        return;
    }

    fileInput.addEventListener("change", () => {
        if (fileInput.files.length > 0) {
            fileNameDisplay.textContent = `Fichier sélectionné : ${fileInput.files[0].name}`;
            fileNameDisplay.style.display = "block";
        }
    });

    uploadForm.addEventListener("submit", (e) => {
        e.preventDefault();
        if (fileInput.files.length === 0) {
            alert("Veuillez choisir un fichier.");
            return;
        }

        const formData = new FormData();
        formData.append("file", fileInput.files[0]);
        formData.append("identifiant", identifiant);

        fetch("upload.php", {
            method: "POST",
            body: formData,
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
               
                fileInput.value = "";
                fileNameDisplay.style.display = "none";
                afficherFichiers();
            } else {
                alert("Erreur : " + data.error);
            }
        })
        .catch(error => console.error("Erreur :", error));
    });

    function afficherFichiers() {
        fetch(`upload.php?identifiant=${identifiant}`)
        .then(response => response.json())
        .then(data => {
            uploadedFilesList.innerHTML = "";
            data.forEach(file => {
                const listItem = document.createElement("li");

                const fileLink = document.createElement("a");
                fileLink.href = file.url;
                fileLink.target = "_blank";
                fileLink.textContent = file.url.split('/').pop();

                const deleteButton = document.createElement("img");
                deleteButton.src = "poubelle.jpg";
                deleteButton.alt = "Supprimer";
                deleteButton.classList.add("delete-icon");
                deleteButton.style.cursor = "pointer";
                deleteButton.addEventListener("click", () => supprimerFichier(file.url));

                listItem.appendChild(fileLink);
                listItem.appendChild(deleteButton);
                uploadedFilesList.appendChild(listItem);
            });
        })
        .catch(error => console.error("Erreur :", error));
    }

    function supprimerFichier(fileUrl) {
        if (!confirm("Voulez-vous vraiment supprimer ce fichier ?")) return;

        fetch("upload.php", {
            method: "DELETE",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ url: fileUrl }),
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
               
                afficherFichiers();
            } else {
                alert("Erreur : " + data.error);
            }
        })
        .catch(error => console.error("Erreur :", error));
    }

    afficherFichiers();
});
