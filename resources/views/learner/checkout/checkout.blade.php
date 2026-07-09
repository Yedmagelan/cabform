@extends('layouts.learner')

@section('title', 'Finaliser l\'achat')
@section('page_title', 'Checkout')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-10">
        <!-- Progress Indicator -->
        <div class="card card-instructor p-3 mb-4">
            <div class="d-flex justify-content-between text-center position-relative">
                <div class="position-absolute start-0 end-0 top-50 translate-middle-y bg-secondary" style="height: 2px; z-index: 1;"></div>
                <div class="wizard-step active" style="z-index: 3;">
                    <div class="rounded-circle bg-indigo text-white d-flex align-items-center justify-content-center mx-auto mb-2" style="width: 32px; height: 32px; font-weight: bold;">1</div>
                    <span class="text-white" style="font-size: 0.85rem;">Facturation & Récap</span>
                </div>
                <div class="wizard-step text-muted" style="z-index: 3;">
                    <div class="rounded-circle bg-dark border border-secondary text-muted d-flex align-items-center justify-content-center mx-auto mb-2" style="width: 32px; height: 32px;">2</div>
                    <span style="font-size: 0.85rem;">Paiement Sécurisé</span>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <!-- Form Details -->
            <div class="col-md-7">
                <div class="card card-instructor p-4">
                    <h5 class="fw-bold text-white mb-4"><i class="fas fa-file-invoice text-indigo me-2"></i>Adresse de Facturation</h5>

                    <form id="checkout-payment-form" action="{{ route('payment.initiate', $course->slug) }}" method="POST">
                        @csrf
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label text-white">Prénom</label>
                                <input type="text" name="first_name" class="form-control bg-dark border-secondary text-white py-2" value="{{ $user->first_name }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-white">Nom de famille</label>
                                <input type="text" name="last_name" class="form-control bg-dark border-secondary text-white py-2" value="{{ $user->last_name }}" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label text-white">Adresse Email</label>
                            <input type="email" class="form-control bg-dark border-secondary text-muted py-2" value="{{ $user->email }}" disabled>
                        </div>

                        <div class="mb-3">
                            <label class="form-label text-white">Téléphone (requis pour Mobile Money)</label>
                            <input type="text" name="phone" class="form-control bg-dark border-secondary text-white py-2" value="{{ $user->phone }}" placeholder="ex: +225 07070707" required>
                        </div>

                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label class="form-label text-white">Ville</label>
                                <input type="text" name="city" class="form-control bg-dark border-secondary text-white" value="{{ $user->profile->city ?? '' }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-white">Pays</label>
                                <select name="country" class="form-select bg-dark border-secondary text-white" required>
                                    <option value="CI" selected>Côte d'Ivoire</option>
                                    <option value="SN">Sénégal</option>
                                    <option value="CM">Cameroun</option>
                                    <option value="TG">Togo</option>
                                    <option value="BF">Burkina Faso</option>
                                </select>
                            </div>
                        </div>

                        <h5 class="fw-bold text-white mb-3"><i class="fas fa-credit-card text-indigo me-2"></i>Moyen de paiement</h5>
                        <div class="d-flex flex-column gap-2 mb-4">
                            <label class="d-flex align-items-center gap-3 p-3 rounded bg-dark border border-secondary cursor-pointer">
                                <input type="radio" name="payment_method" value="cinetpay" checked class="form-check-input">
                                <div>
                                    <strong class="text-white d-block">Mobile Money / Visa</strong>
                                    <small class="text-muted">Orange, MTN, Moov, Wave et cartes bancaires</small>
                                </div>
                            </label>
                        </div>

                        <button type="submit" class="btn btn-premium w-100 py-3 fw-bold fs-6"><i class="fas fa-lock me-2"></i>Procéder au Paiement sécurisé</button>
                    </form>
                </div>
            </div>

            <!-- Cart recap -->
            <div class="col-md-5">
                <div class="card card-instructor p-4 text-white">
                    <h5 class="fw-bold mb-4">Récapitulatif de la commande</h5>
                    
                    <div class="p-3 bg-dark border border-secondary rounded d-flex gap-3 align-items-center mb-4">
                        @if($course->thumbnail)
                            <img src="{{ asset('storage/' . $course->thumbnail) }}" class="rounded" style="width: 50px; height: 50px; object-fit: cover;">
                        @else
                            <div class="bg-secondary rounded d-flex align-items-center justify-content-center text-white" style="width: 50px; height: 50px;">
                                <i class="fas fa-graduation-cap"></i>
                            </div>
                        @endif
                        <div class="flex-grow-1">
                            <strong class="d-block" style="font-size: 0.9rem;">{{ $course->title }}</strong>
                            <small class="text-muted">{{ $course->level_label }}</small>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Prix de formation :</span>
                        <strong>{{ number_format($course->price, 0, ',', ' ') }} FCFA</strong>
                    </div>
                    
                    <div class="d-flex justify-content-between mb-3 border-bottom border-secondary pb-2">
                        <span class="text-muted">TVA (0%) :</span>
                        <strong>0 FCFA</strong>
                    </div>

                    <div class="d-flex justify-content-between">
                        <span class="fw-bold fs-5">TOTAL :</span>
                        <strong class="fw-bold fs-5 text-indigo">{{ number_format($course->price, 0, ',', ' ') }} FCFA</strong>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
