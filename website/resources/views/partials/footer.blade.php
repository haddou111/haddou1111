<footer class="bg-dark text-white py-4 mt-5">
    <div class="container">
        <div class="row">
            <div class="col-md-4">
                <h5>Contact</h5>
                <p>
                    <i class="fas fa-map-marker-alt me-2"></i> Adresse de l'ESTK<br>
                    <i class="fas fa-phone me-2"></i> Téléphone<br>
                    <i class="fas fa-envelope me-2"></i> Email
                </p>
            </div>
            <div class="col-md-4">
                <h5>Liens rapides</h5>
                <ul class="list-unstyled">
                    <li><a href="{{ route('home') }}" class="text-white"><i class="fas fa-home me-2"></i>Accueil</a></li>
                    <li><a href="{{ route('about') }}" class="text-white"><i class="fas fa-info-circle me-2"></i>À propos</a></li>
                    <li><a href="{{ route('emplois-du-temps') }}" class="text-white"><i class="fas fa-calendar-alt me-2"></i>Emplois du temps</a></li>
                    <li><a href="{{ route('avis') }}" class="text-white"><i class="fas fa-comments me-2"></i>Avis</a></li>
                </ul>
            </div>
            <div class="col-md-4">
                <h5>Suivez-nous</h5>
                <div class="social-links">
                    <a href="#" class="text-white me-3">
                        <img src="{{ asset('images/icons/facebook.png') }}" alt="Facebook" height="24">
                    </a>
                    <a href="#" class="text-white me-3">
                        <img src="{{ asset('images/icons/twitter.png') }}" alt="Twitter" height="24">
                    </a>
                    <a href="#" class="text-white">
                        <img src="{{ asset('images/icons/instagram.png') }}" alt="Instagram" height="24">
                    </a>
                </div>
            </div>
        </div>
        <hr>
        <div class="text-center">
            <img src="{{ asset('images/logo/estk-logo.png') }}" alt="ESTK Logo" height="40" class="mb-3">
            <p>&copy; {{ date('Y') }} ESTK. Tous droits réservés.</p>
        </div>
    </div>
</footer> 