image.png// Script pour le diaporama d'images
document.addEventListener('DOMContentLoaded', function() {
    // Tableau des images du diaporama
    const images = ['EST_KHENIFRA.jpg', '2222.jpg', '3333.jpg'];
    const slideshowImage = document.getElementById('slideshow-image');
    const prevButton = document.getElementById('prev-button');
    const nextButton = document.getElementById('next-button');
    const dots = document.querySelectorAll('.dot');
    
    let currentIndex = 0;
    let isAnimating = false;
    
    // Préchargement des images pour une transition fluide
    function preloadImages() {
        images.forEach(src => {
            const img = new Image();
            img.src = src;
        });
    }
    
    // Fonction pour afficher l'image avec animation
    function showImage(index, direction) {
        if (isAnimating) return;
        isAnimating = true;
        
        console.log('Changing to image: ' + images[index] + ' (direction: ' + direction + ')');
        
        // Appliquer l'animation de sortie
        if (direction === 'next') {
            slideshowImage.classList.add('slideLeft');
        } else if (direction === 'prev') {
            slideshowImage.classList.add('slideRight');
        } else {
            slideshowImage.classList.add('fadeOut');
        }
        
        // Mettre à jour l'image après un court délai
        setTimeout(() => {
            slideshowImage.src = images[index];
            
            // Supprimer les classes d'animation précédentes
            slideshowImage.classList.remove('slideLeft', 'slideRight', 'fadeOut');
            
            // Forcer un reflow pour appliquer la nouvelle animation
            void slideshowImage.offsetWidth;
            
            // Appliquer l'animation d'entrée
            slideshowImage.classList.add('fadeIn');
            
            // Mise à jour des points indicateurs
            dots.forEach(dot => dot.classList.remove('active'));
            dots[index].classList.add('active');
            
            // Réinitialiser le drapeau d'animation
            setTimeout(() => {
                slideshowImage.classList.remove('fadeIn');
                isAnimating = false;
            }, 800);
        }, 400);
    }
    
    // Bouton précédent
    prevButton.addEventListener('click', function() {
        console.log('Previous button clicked');
        const newIndex = (currentIndex - 1 + images.length) % images.length;
        showImage(newIndex, 'prev');
        currentIndex = newIndex;
    });
    
    // Bouton suivant
    nextButton.addEventListener('click', function() {
        console.log('Next button clicked');
        const newIndex = (currentIndex + 1) % images.length;
        showImage(newIndex, 'next');
        currentIndex = newIndex;
    });
    
    // Navigation par les points
    dots.forEach(dot => {
        dot.addEventListener('click', function() {
            const newIndex = parseInt(this.getAttribute('data-index'));
            if (newIndex === currentIndex) return;
            
            const direction = newIndex > currentIndex ? 'next' : 'prev';
            showImage(newIndex, direction);
            currentIndex = newIndex;
        });
    });
    
    // Précharger les images
    preloadImages();
});

// Script pour la validation du formulaire newsletter
document.addEventListener('DOMContentLoaded', function() {
    const newsletterForm = document.getElementById('newsletter-form');
    const emailInput = document.getElementById('newsletter-email');
    
    newsletterForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Réinitialiser l'état précédent
        emailInput.classList.remove('show-error');
        
        // Vérifier si le champ est vide
        if (!emailInput.value.trim()) {
            emailInput.classList.add('show-error');
            return false;
        }
        
        // Vérifier le format d'email
        const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailPattern.test(emailInput.value)) {
            emailInput.classList.add('show-error');
            document.getElementById('email-error').textContent = 'Veuillez saisir une adresse email valide.';
            return false;
        }
        
        // Si tout est valide, soumettre le formulaire
        alert('Inscription réussie! Merci de vous être abonné à notre newsletter.');
        newsletterForm.reset();
    });
    
    // Montrer le message d'erreur si l'utilisateur quitte le champ sans le remplir
    emailInput.addEventListener('blur', function() {
        if (!this.value.trim()) {
            this.classList.add('show-error');
        } else {
            this.classList.remove('show-error');
        }
    });
    
    // Cacher le message d'erreur lorsque l'utilisateur commence à taper
    emailInput.addEventListener('input', function() {
        this.classList.remove('show-error');
    });
});

// Script pour le slider d'actualités
document.addEventListener('DOMContentLoaded', function() {
    const cardsContainer = document.querySelector('.news-cards-container');
    const prevButton = document.querySelector('.news-nav-button.prev');
    const nextButton = document.querySelector('.news-nav-button.next');
    const cards = document.querySelectorAll('.news-card');
    const cardWidth = cards[0].offsetWidth + 30; // +30 pour le gap
    const totalCards = cards.length;
    const visibleCards = 3;
    let currentIndex = 0;
    
    // Fonction pour désactiver les boutons au besoin
    function updateButtonsState() {
        prevButton.disabled = currentIndex === 0;
        nextButton.disabled = currentIndex >= totalCards - visibleCards;
        
        prevButton.style.opacity = prevButton.disabled ? '0.5' : '1';
        nextButton.style.opacity = nextButton.disabled ? '0.5' : '1';
        prevButton.style.cursor = prevButton.disabled ? 'default' : 'pointer';
        nextButton.style.cursor = nextButton.disabled ? 'default' : 'pointer';
    }
    
    // Fonction pour faire défiler à gauche
    prevButton.addEventListener('click', function() {
        if (currentIndex > 0) {
            currentIndex--;
            cardsContainer.scrollTo({
                left: cardWidth * currentIndex,
                behavior: 'smooth'
            });
            updateButtonsState();
        }
    });
    
    // Fonction pour faire défiler à droite
    nextButton.addEventListener('click', function() {
        if (currentIndex < totalCards - visibleCards) {
            currentIndex++;
            cardsContainer.scrollTo({
                left: cardWidth * currentIndex,
                behavior: 'smooth'
            });
            updateButtonsState();
        }
    });
    
    // Initialiser l'état des boutons
    updateButtonsState();
    
    // Gérer le redimensionnement de la fenêtre
    window.addEventListener('resize', function() {
        // Recalculer la largeur des cartes après redimensionnement
        const newCardWidth = cards[0].offsetWidth + 30;
        
        // Réinitialiser la position de défilement
        cardsContainer.scrollTo({
            left: newCardWidth * currentIndex,
            behavior: 'instant'
        });
    });
});

// Navigation entre les sections
document.addEventListener('DOMContentLoaded', function() {
    const navLinks = document.querySelectorAll('.top-bar nav ul li a');
    const pageSections = document.querySelectorAll('.page-section');

    // Fonction pour afficher une section
    function showSection(sectionId) {
        // Cacher toutes les sections
        pageSections.forEach(section => {
            section.classList.remove('active');
        });

        // Désactiver tous les liens
        navLinks.forEach(link => {
            link.classList.remove('active');
        });

        // Afficher la section demandée
        const targetSection = document.getElementById(sectionId);
        if (targetSection) {
            targetSection.classList.add('active');
        }

        // Activer le lien correspondant
        const activeLink = document.querySelector(`a[href="#${sectionId}"]`);
        if (activeLink) {
            activeLink.classList.add('active');
        }
    }

    // Gérer les clics sur les liens de navigation
    navLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const sectionId = this.getAttribute('href').substring(1);
            showSection(sectionId);
        });
    });

    // Afficher la section accueil par défaut
    showSection('accueil');
});
