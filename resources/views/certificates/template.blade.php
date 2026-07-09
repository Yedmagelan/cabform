<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Helvetica Neue', Arial, sans-serif; background: #ffffff; }
        .certificate { width: 100%; height: 100%; position: relative; padding: 60px 80px; border: 3px solid #0500d8; }
        .certificate::before { content: ''; position: absolute; top: 8px; left: 8px; right: 8px; bottom: 8px; border: 1px solid rgba(5,0,216,0.3); }
        .header { text-align: center; margin-bottom: 30px; }
        .header img { height: 50px; margin-bottom: 10px; }
        .header h1 { font-size: 36px; color: #0500d8; letter-spacing: 3px; text-transform: uppercase; }
        .header .subtitle { font-size: 14px; color: #666; letter-spacing: 2px; text-transform: uppercase; margin-top: 5px; }
        .body { text-align: center; margin: 30px 0; }
        .body .label { font-size: 14px; color: #888; margin-bottom: 10px; }
        .body .name { font-size: 32px; font-weight: 700; color: #00000f; border-bottom: 2px solid #0500d8; display: inline-block; padding: 0 20px 10px; margin-bottom: 20px; }
        .body .course-title { font-size: 18px; color: #333; margin: 15px 0; }
        .body .course-name { font-size: 22px; font-weight: 700; color: #0500d8; }
        .body .score { font-size: 14px; color: #666; margin-top: 10px; }
        .details { display: flex; justify-content: space-between; align-items: flex-end; margin-top: 40px; }
        .details .left { text-align: left; }
        .details .right { text-align: right; }
        .details .cert-number { font-size: 11px; color: #999; }
        .details .date { font-size: 12px; color: #666; margin-top: 4px; }
        .signatory { font-size: 12px; color: #666; border-top: 1px solid #333; padding-top: 8px; min-width: 200px; }
        .signatory .name { font-size: 14px; font-weight: 700; color: #333; border: none; padding: 0; display: block; }
        .signatory .title { font-size: 11px; color: #888; }
        .qr-code { text-align: center; margin-top: 20px; }
        .qr-code img { width: 80px; height: 80px; }
        .qr-code .verify { font-size: 9px; color: #999; margin-top: 4px; }
        .watermark { position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%) rotate(-30deg); font-size: 120px; color: rgba(5,0,216,0.03); font-weight: 900; letter-spacing: 10px; pointer-events: none; }
    </style>
</head>
<body>
    <div class="certificate">
        <div class="watermark">CABFORM</div>

        <div class="header">
            @if($template && $template->logo_path)
                <img src="{{ public_path($template->logo_path) }}" alt="Logo">
            @endif
            <h1>Certificat de Réussite</h1>
            <div class="subtitle">Formation Professionnelle Certifiante</div>
        </div>

        <div class="body">
            <div class="label">Ce certificat est décerné à</div>
            <div class="name">{{ $user->full_name }}</div>

            <div class="course-title">Pour avoir complété avec succès la formation</div>
            <div class="course-name">{{ $course->title }}</div>

            @if($certificate->final_score)
                <div class="score">Score obtenu : <strong>{{ number_format($certificate->final_score, 0) }}%</strong></div>
            @endif
        </div>

        <div class="details">
            <div class="left">
                <div class="cert-number">N° {{ $certificate->certificate_number }}</div>
                <div class="date">Délivré le {{ $certificate->issued_at?->format('d/m/Y') }}</div>
                @if($certificate->expires_at)
                    <div class="date">Valide jusqu'au {{ $certificate->expires_at->format('d/m/Y') }}</div>
                @endif
            </div>
            <div class="right">
                <div class="signatory">
                    <div class="name">{{ $template->signatory_name ?? 'Direction CabForm' }}</div>
                    <div class="title">{{ $template->signatory_title ?? 'Directeur de la Formation' }}</div>
                </div>
            </div>
        </div>

        <div class="qr-code">
            @if(!app()->environment('testing'))
                <img src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data={{ urlencode($certificate->verification_url) }}" alt="QR Code" style="width: 80px; height: 80px; margin-bottom: 5px;">
            @endif
            <div class="verify">Vérifier l'authenticité : {{ $certificate->verification_url }}</div>
        </div>
    </div>
</body>
</html>
