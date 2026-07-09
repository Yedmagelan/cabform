@extends('layouts.instructor')

@section('title', 'Centre de Notifications')
@section('page_title', 'Centre de Notifications')

@section('content')
<div class="row g-4">
    <!-- List of Notifications -->
    <div class="col-lg-8">
        <div class="card card-instructor p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="fw-bold text-white mb-0">Vos notifications récentes</h5>
                <form action="{{ route('instructor.notifications.mark-all-read') }}" method="POST">@csrf
                    <button type="submit" class="btn btn-sm btn-outline-secondary border-secondary text-white"><i class="fas fa-check-double me-2"></i>Tout marquer comme lu</button>
                </form>
            </div>

            <div class="d-flex flex-column gap-3">
                @forelse($notifications as $notif)
                    <div class="p-3 bg-dark border border-secondary rounded d-flex justify-content-between align-items-start {{ $notif->read() ? 'opacity-75' : '' }}">
                        <div>
                            <div class="d-flex align-items-center gap-2 mb-1">
                                <span class="fw-bold text-white" style="font-size: 0.95rem;">{{ $notif->data['title'] ?? 'Alerte' }}</span>
                                @if(!$notif->read())
                                    <span class="badge bg-danger rounded-pill" style="font-size: 0.65rem;">Nouveau</span>
                                @endif
                            </div>
                            <p class="mb-2 text-muted" style="font-size: 0.85rem;">{{ $notif->data['message'] ?? '' }}</p>
                            <span class="text-muted" style="font-size: 0.75rem;"><i class="fas fa-clock me-1"></i>{{ $notif->created_at->diffForHumans() }}</span>
                        </div>
                        <div class="d-flex gap-2">
                            @if(!$notif->read())
                                <form action="{{ route('instructor.notifications.read', $notif->id) }}" method="POST">@csrf
                                    <button type="submit" class="btn btn-sm btn-outline-secondary border-secondary text-white"><i class="fas fa-eye"></i></button>
                                </form>
                            @endif
                            <form action="{{ route('instructor.notifications.delete', $notif->id) }}" method="POST">@csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger border-danger text-danger"><i class="fas fa-trash"></i></button>
                            </form>
                        </div>
                    </div>
                @empty
                    <div class="p-5 text-center text-muted">
                        <i class="fas fa-bell-slash d-block mb-3" style="font-size: 2.5rem;"></i>
                        Aucune notification.
                    </div>
                @endforelse
            </div>

            <div class="mt-4">
                {{ $notifications->links() }}
            </div>
        </div>
    </div>

    <!-- Notification Settings preferences -->
    <div class="col-lg-4">
        <div class="card card-instructor p-4">
            <h5 class="fw-bold text-white mb-4"><i class="fas fa-cog text-indigo me-2"></i>Préférences d'alerte</h5>
            
            <form action="{{ route('instructor.notifications.settings') }}" method="POST">
                @csrf
                <div class="mb-3 border-bottom border-secondary pb-3">
                    <label class="form-label text-white fw-bold">Sélection des canaux</label>
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" name="notif_channels[]" value="in_app" id="ch-app" checked>
                        <label class="form-check-label text-muted" for="ch-app">In-app (Cloche de notification)</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="notif_channels[]" value="email" id="ch-mail" checked>
                        <label class="form-check-label text-muted" for="ch-mail">Adresse email principale</label>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label text-white fw-bold">Notifier pour :</label>
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" name="notif_submission" id="n-sub" checked>
                        <label class="form-check-label text-muted" for="n-sub">Remise de devoir (soumission)</label>
                    </div>
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" name="notif_forum" id="n-forum" checked>
                        <label class="form-check-label text-muted" for="n-forum">Nouvelles questions forum</label>
                    </div>
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" name="notif_message" id="n-msg" checked>
                        <label class="form-check-label text-muted" for="n-msg">Messages directs d'apprenants</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="notif_inactive" id="n-in" checked>
                        <label class="form-check-label text-muted" for="n-in">Apprenant inactif (7j sans accès)</label>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label text-white fw-bold">Fréquence des résumés</label>
                    <select name="notif_frequency" class="form-select bg-dark border-secondary text-white">
                        <option value="immediate" selected>Immédiate (Temps réel)</option>
                        <option value="daily">Quotidienne (Chaque soir)</option>
                        <option value="weekly">Hebdomadaire (Le vendredi)</option>
                    </select>
                </div>

                <button type="submit" class="btn btn-premium w-100 py-2"><i class="fas fa-save me-2"></i>Enregistrer les préférences</button>
            </form>
        </div>
    </div>
</div>
@endsection
