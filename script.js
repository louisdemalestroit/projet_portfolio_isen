document.addEventListener('DOMContentLoaded', () => {
    const dropdownButton = document.querySelector('.dropdown-button3');
    const dropdownMenu = document.querySelector('.dropdown-menu3');

    // Vérifiez que le bouton et le menu existent
    if (dropdownButton && dropdownMenu) {
        dropdownButton.addEventListener('click', (event) => {
            event.stopPropagation(); // Empêche la fermeture immédiate du menu au clic
            dropdownMenu.style.display = dropdownMenu.style.display === 'block' ? 'none' : 'block';
        });

        // Fermer le menu si l'utilisateur clique ailleurs
        document.addEventListener('click', (event) => {
            if (!dropdownButton.contains(event.target) && !dropdownMenu.contains(event.target)) {
                dropdownMenu.style.display = 'none';
            }
        });
    }
});
