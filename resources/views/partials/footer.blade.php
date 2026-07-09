<!-- Footer CabForm -->
<footer class="footer-cabform">
    <div class="container">
        <div class="row g-5">
            <!-- Brand -->
            <div class="col-lg-4 col-md-6">
                <div class="mb-4">
                    <img src="{{ asset('assets/img/Logo-CabForm.png') }}" alt="CabForm" height="45" class="mb-3">
                    <p class="text-cb-muted" style="font-size: 0.9rem; max-width: 300px;">
                        Plateforme de formation et certification en ligne. Développez vos compétences avec des formations certifiantes de qualité.
                    </p>
                </div>
                <div class="footer-social">
                    <a href="#" title="Facebook"><i class="fab fa-facebook-f"></i></a>
                    <a href="#" title="LinkedIn"><i class="fab fa-linkedin-in"></i></a>
                    <a href="#" title="Twitter"><i class="fab fa-twitter"></i></a>
                    <a href="#" title="YouTube"><i class="fab fa-youtube"></i></a>
                    <a href="#" title="Instagram"><i class="fab fa-instagram"></i></a>
                </div>
            </div>

            <!-- Formations -->
            <div class="col-lg-2 col-md-6">
                <h5>Formations</h5>
                <a href="{{ url('/catalog') }}" class="footer-link">Catalogue</a>

                <a href="{{ url('/catalog?filter=free') }}" class="footer-link">Formations gratuites</a>
                <a href="{{ url('/catalog?filter=certified') }}" class="footer-link">Certifications</a>
                <a href="{{ url('/verify-certificate') }}" class="footer-link">Vérifier un certificat</a>
            </div>

            <!-- Entreprise -->
            <div class="col-lg-2 col-md-6">
                <h5>Entreprise</h5>
                <a href="{{ url('/about') }}" class="footer-link">À propos</a>
                <a href="{{ url('/contact') }}" class="footer-link">Contact</a>
                <a href="{{ url('/blog') }}" class="footer-link">Blog</a>
                <a href="{{ url('/faq') }}" class="footer-link">FAQ</a>
                
            </div>

            <!-- Légal -->
            <div class="col-lg-2 col-md-6">
                <h5>Légal</h5>
                <a href="{{ route('legal.mentions') }}" class="footer-link">Mentions légales</a>
                <a href="{{ route('legal.cgu') }}" class="footer-link">CGU / CGV</a>
                <a href="{{ route('legal.confidentialite') }}" class="footer-link">Confidentialité</a>
                <a href="{{ route('legal.cookies') }}" class="footer-link">Politique cookies</a>
            </div>

            <!-- Contact -->
            <div class="col-lg-2 col-md-6">
                <h5>Contact</h5>
                <div class="text-cb-muted" style="font-size: 0.85rem;">
                    <p class="mb-2"><i class="fas fa-map-marker-alt me-2 text-cb-primary"></i>Abidjan, Côte d'Ivoire</p>
                    <p class="mb-2"><i class="fas fa-envelope me-2 text-cb-primary"></i>contact@cabform.com</p>
                    <p class="mb-2"><i class="fas fa-phone me-2 text-cb-primary"></i>+225 XX XX XX XX</p>
                </div>
            </div>
        </div>

        <!-- Bottom -->
        <div class="footer-bottom text-center text-cb-muted" style="font-size: 0.85rem;">
            <p class="mb-0">
                &copy; {{ date('Y') }} <strong class="text-cb-primary">CabForm</strong>. Tous droits réservés. Made in Côte d'Ivoire.
            </p>
        </div>
    </div>
</footer>
