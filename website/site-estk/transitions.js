// Fonction pour gérer la transition entre les pages
function transition(event, destination) {
    event.preventDefault();
    document.body.classList.add('fade-out');
    setTimeout(() => {
        window.location.href = destination;
    }, 500);
}

// Fonction pour gérer le survol des liens de navigation
function handleNavHover(element, isHovering) {
    element.style.color = isHovering ? '#EA9A07' : 'white';
}

// Ajouter les écouteurs d'événements une fois que le DOM est chargé
document.addEventListener('DOMContentLoaded', function() {
    // Trouver tous les liens de navigation qui doivent avoir un effet de survol
    const navLinks = document.querySelectorAll('.top-bar nav ul li a');
    
    navLinks.forEach(link => {
        // Ne pas ajouter l'effet de survol aux liens déjà colorés ou avec la classe highlight
        if (!link.classList.contains('highlight') && link.style.color !== '#EA9A07') {
            link.style.transition = 'color 0.3s ease';
            link.addEventListener('mouseover', () => handleNavHover(link, true));
            link.addEventListener('mouseout', () => handleNavHover(link, false));
        }
    });
}); 