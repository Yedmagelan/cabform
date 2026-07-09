@extends('layouts.app')
@section('title', 'Contact')
@section('content')
<section style="padding: 120px 0 60px;">
    <div class="container">
        <div class="text-center mb-5 fade-in">
            <span class="section-subtitle"><i class="fas fa-envelope me-2"></i>Contactez-nous</span>
            <h1 class="section-title">Besoin d'<span class="text-gradient">aide</span> ?</h1>
            <p class="section-description">Notre équipe est à votre disposition pour répondre à toutes vos questions.</p>
        </div>
        <div class="row g-5">
            <div class="col-lg-5 fade-in-left">
                <div class="card-cabform p-4 mb-4">
                    <div class="d-flex align-items-center gap-3 mb-3"><div class="card-icon primary"><i class="fas fa-map-marker-alt"></i></div><div><h6 class="fw-600 mb-0">Adresse</h6><p class="text-cb-muted mb-0" style="font-size:0.9rem;">Abidjan, Côte d'Ivoire</p></div></div>
                    <div class="d-flex align-items-center gap-3 mb-3"><div class="card-icon success"><i class="fas fa-envelope"></i></div><div><h6 class="fw-600 mb-0">E-mail</h6><p class="text-cb-muted mb-0" style="font-size:0.9rem;">contact@cabform.com</p></div></div>
                    <div class="d-flex align-items-center gap-3"><div class="card-icon warning"><i class="fas fa-phone"></i></div><div><h6 class="fw-600 mb-0">Téléphone</h6><p class="text-cb-muted mb-0" style="font-size:0.9rem;">+225 XX XX XX XX</p></div></div>
                </div>
                <div class="card-cabform p-4">
                    <h6 class="fw-700 mb-3">Suivez-nous</h6>
                    <div class="footer-social">
                        <a href="#"><i class="fab fa-facebook-f"></i></a>
                        <a href="#"><i class="fab fa-linkedin-in"></i></a>
                        <a href="#"><i class="fab fa-twitter"></i></a>
                        <a href="#"><i class="fab fa-youtube"></i></a>
                    </div>
                </div>
            </div>
            <div class="col-lg-7 fade-in-right">
                <div class="card-cabform p-4">
                    <h5 class="fw-700 mb-4">Envoyez-nous un message</h5>
                    <div id="contact-success" class="alert border-0 rounded-cb d-none" style="background:rgba(0,217,126,0.1);color:var(--cb-success);"></div>
                    <form id="contact-form">
                        @csrf
                        <div class="row g-3">
                            <div class="col-md-6"><label class="form-label-cabform">Nom complet</label><input type="text" name="name" class="form-control form-control-cabform" placeholder="Votre nom" required></div>
                            <div class="col-md-6"><label class="form-label-cabform">E-mail</label><input type="email" name="email" class="form-control form-control-cabform" placeholder="votre@email.com" required></div>
                            <div class="col-12"><label class="form-label-cabform">Sujet</label><input type="text" name="subject" class="form-control form-control-cabform" placeholder="Sujet de votre message" required></div>
                            <div class="col-12"><label class="form-label-cabform">Message</label><textarea name="message" class="form-control form-control-cabform" rows="5" placeholder="Décrivez votre demande..." required></textarea></div>
                        </div>
                        <button type="submit" class="btn btn-cabform btn-cabform-primary btn-cabform-lg mt-4 w-100"><i class="fas fa-paper-plane me-2"></i>Envoyer</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
@push('scripts')
<script>
$('#contact-form').on('submit', function(e) {
    e.preventDefault();
    var $btn = $(this).find('button[type="submit"]');
    $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i>Envoi...');
    $.post('{{ route("contact.submit") }}', $(this).serialize(), function(res) {
        if (res.success) { $('#contact-success').removeClass('d-none').html('<i class="fas fa-check-circle me-2"></i>' + res.message); $('#contact-form')[0].reset(); }
    }).fail(function() { showToast('Erreur lors de l\'envoi.', 'error'); }).always(function() { $btn.prop('disabled', false).html('<i class="fas fa-paper-plane me-2"></i>Envoyer'); });
});
</script>
@endpush
