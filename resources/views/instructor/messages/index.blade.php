@extends('layouts.instructor')

@section('title', 'Messagerie')
@section('page_title', 'Messagerie Pédagogique')

@section('content')
<div class="card card-instructor p-0 overflow-hidden" style="height: calc(100vh - 140px); min-height: 480px;">
    <div class="row g-0 h-100">
        <!-- Contacts Sidebar -->
        <div class="col-md-4 border-end border-secondary d-flex flex-column h-100 bg-light" style="background: var(--cb-dark-secondary) !important;">
            <div class="p-3 border-bottom border-secondary">
                <input type="text" id="contact-search" class="form-control border-secondary text-dark btn-sm" placeholder="Rechercher une conversation...">
            </div>
            
            <div class="flex-grow-1 overflow-y-auto" style="max-height: 100%;">
                <div class="list-group list-group-flush" id="contacts-list-group">
                    @forelse($contacts as $contact)
                        <a href="{{ route('instructor.messages.index', ['contact_id' => $contact->id]) }}" class="list-group-item list-group-item-action border-0 p-3 d-flex align-items-center gap-3 {{ $activeContact && $activeContact->id === $contact->id ? 'bg-light' : '' }}" style="{{ $activeContact && $activeContact->id === $contact->id ? 'background: rgba(var(--cb-primary-rgb), 0.12) !important; font-weight: 600;' : 'background: transparent;' }}">
                            <div class="user-avatar" style="width: 40px; height: 40px; background: var(--cb-gradient-primary);">{{ $contact->initials }}</div>
                            <div class="flex-grow-1 text-truncate">
                                <div class="d-flex justify-content-between align-items-center">
                                    <strong class="text-dark text-truncate">{{ $contact->full_name }}</strong>
                                </div>
                                <small class="text-muted text-truncate d-block">{{ $contact->email }}</small>
                            </div>
                        </a>
                    @empty
                        <div class="p-4 text-center text-muted">Aucune discussion en cours.</div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Chat Workspace -->
        <div class="col-md-8 d-flex flex-column h-100">
            @if($activeContact)
                <!-- Header -->
                <div class="p-3 border-bottom border-secondary d-flex justify-content-between align-items-center" style="background: var(--cb-dark-card) !important;">
                    <div class="d-flex align-items-center gap-3">
                        <div class="user-avatar" style="background: var(--cb-gradient-primary);">{{ $activeContact->initials }}</div>
                        <div>
                            <h6 class="fw-bold text-dark mb-0">{{ $activeContact->full_name }}</h6>
                            <small class="text-muted">{{ $activeContact->email }}</small>
                        </div>
                    </div>
                </div>

                <!-- Messages area -->
                <div class="flex-grow-1 p-4 overflow-y-auto d-flex flex-column gap-3" id="messages-container" style="background: var(--cb-dark);">
                    @forelse($messages as $msg)
                        @php
                            $isMe = $msg->sender_id === auth()->id();
                        @endphp
                        <div class="d-flex {{ $isMe ? 'justify-content-end' : 'justify-content-start' }}">
                            <div class="p-3 rounded-3 max-w-75" style="background: {{ $isMe ? 'var(--cb-primary)' : 'var(--cb-dark-secondary)' }}; color: {{ $isMe ? '#fff' : 'var(--cb-text-primary)' }}; border: {{ $isMe ? 'none' : '1px solid var(--cb-glass-border)' }}">
                                <p class="mb-1" style="font-size: 0.9rem; white-space: pre-wrap;">{{ $msg->body }}</p>
                                <div class="text-end" style="font-size: 0.7rem; opacity: 0.6;">
                                    {{ $msg->created_at->format('H:i') }}
                                    @if($isMe)
                                        <i class="fas {{ $msg->is_read ? 'fa-check-double text-success' : 'fa-check' }} ms-1"></i>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="my-auto text-center text-muted">Envoyez un message pour débuter la discussion.</div>
                    @endforelse
                </div>

                <!-- Input area -->
                <div class="p-3 border-top border-secondary" style="background: var(--cb-dark-card) !important;">
                    <form action="{{ route('instructor.messages.send') }}" method="POST">
                        @csrf
                        <input type="hidden" name="receiver_id" value="{{ $activeContact->id }}">
                        <div class="input-group">
                            <textarea name="body" class="form-control border-secondary text-dark" rows="2" placeholder="Saisir votre message..." required></textarea>
                            <button type="submit" class="btn btn-premium px-4"><i class="fas fa-paper-plane"></i></button>
                        </div>
                    </form>
                </div>
            @else
                <div class="my-auto text-center text-muted">
                    <i class="fas fa-comments d-block mb-3" style="font-size: 3rem; color: var(--cb-primary);"></i>
                    Sélectionnez une discussion pour commencer à échanger.
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const container = document.getElementById('messages-container');
        if (container) {
            container.scrollTop = container.scrollHeight;
        }

        // Contact filtering
        $('#contact-search').on('input', function() {
            const query = $(this).val().toLowerCase();
            $('#contacts-list-group a').each(function() {
                const text = $(this).text().toLowerCase();
                if (text.indexOf(query) !== -1) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            });
        });
    });
</script>
@endpush
