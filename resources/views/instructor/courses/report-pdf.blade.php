<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Rapport de Performance de Formation</title>
    <style>
        body { font-family: 'Helvetica', sans-serif; color: #333; }
        .header { text-align: center; border-bottom: 2px solid #4f46e5; padding-bottom: 20px; margin-bottom: 30px; }
        .header h2 { margin: 0; color: #4f46e5; }
        .section { margin-bottom: 25px; }
        .section-title { font-weight: bold; font-size: 1.1rem; border-bottom: 1px solid #ddd; padding-bottom: 5px; margin-bottom: 12px; color: #1e293b; }
        .kpi-table { width: 100%; margin-bottom: 25px; }
        .kpi-card { background: #f8fafc; border: 1px solid #e2e8f0; padding: 15px; text-align: center; border-radius: 6px; }
        .kpi-value { font-size: 1.5rem; font-weight: bold; color: #4f46e5; margin-bottom: 4px; }
        .data-table { width: 100%; border-collapse: collapse; }
        .data-table th, .data-table td { border: 1px solid #e2e8f0; padding: 10px 12px; text-align: left; font-size: 0.9rem; }
        .data-table th { background-color: #f8fafc; font-weight: bold; }
    </style>
</head>
<body>
    <div class="header">
        <h2>Rapport de Performance Global</h2>
        <p>Formation : <strong>{{ $course->title }}</strong> &bull; Date : {{ date('d/m/Y') }}</p>
    </div>

    <!-- KPIs -->
    <div class="section">
        <table class="kpi-table" cellspacing="10">
            <tr>
                <td style="width: 33%;">
                    <div class="kpi-card">
                        <div class="kpi-value">{{ $course->enrollments()->count() }}</div>
                        <div style="font-size: 0.8rem; color: #64748b;">Total Apprenants</div>
                    </div>
                </td>
                <td style="width: 33%;">
                    <div class="kpi-card">
                        <div class="kpi-value">{{ round($course->enrollments()->avg('progress_percentage') ?? 0, 1) }}%</div>
                        <div style="font-size: 0.8rem; color: #64748b;">Progression Moyenne</div>
                    </div>
                </td>
                <td style="width: 33%;">
                    <div class="kpi-card">
                        <div class="kpi-value">{{ $course->certificates()->count() }}</div>
                        <div style="font-size: 0.8rem; color: #64748b;">Certificats Générés</div>
                    </div>
                </td>
            </tr>
        </table>
    </div>

    <!-- Students details list -->
    <div class="section">
        <div class="section-title">Liste des Apprenants & Progression</div>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Apprenant</th>
                    <th>Date d'inscription</th>
                    <th>Progression</th>
                    <th>Dernier Accès</th>
                    <th>Statut</th>
                </tr>
            </thead>
            <tbody>
                @forelse($course->enrollments as $enrollment)
                    <tr>
                        <td>{{ $enrollment->user->full_name }}<br><small style="color: #64748b;">{{ $enrollment->user->email }}</small></td>
                        <td>{{ $enrollment->created_at->format('d/m/Y') }}</td>
                        <td><strong>{{ round($enrollment->progress_percentage) }}%</strong></td>
                        <td>{{ $enrollment->last_accessed_at ? $enrollment->last_accessed_at->format('d/m/Y') : '-' }}</td>
                        <td>{{ strtoupper($enrollment->status) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" style="text-align: center; color: #999;">Aucun apprenant inscrit.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</body>
</html>
