<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Rapport de Progression Apprenant</title>
    <style>
        body { font-family: 'Helvetica', sans-serif; color: #333; }
        .header { text-align: center; border-bottom: 2px solid #4f46e5; padding-bottom: 20px; margin-bottom: 30px; }
        .header h2 { margin: 0; color: #4f46e5; }
        .section { margin-bottom: 25px; }
        .section-title { font-weight: bold; font-size: 1.1rem; border-bottom: 1px solid #ddd; padding-bottom: 5px; margin-bottom: 12px; color: #1e293b; }
        .info-table, .data-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .info-table td { padding: 6px 12px; }
        .data-table th, .data-table td { border: 1px solid #e2e8f0; padding: 10px 12px; text-align: left; font-size: 0.9rem; }
        .data-table th { background-color: #f8fafc; font-weight: bold; }
        .badge { display: inline-block; padding: 2px 8px; border-radius: 12px; font-size: 0.75rem; font-weight: bold; }
        .badge-success { background-color: #d1fae5; color: #065f46; }
        .badge-info { background-color: #e0f2fe; color: #0369a1; }
        .badge-secondary { background-color: #f1f5f9; color: #475569; }
    </style>
</head>
<body>
    <div class="header">
        <h2>Rapport de Progression CabForm</h2>
        <p>Généré le {{ date('d/m/Y') }} pour le cours <strong>{{ $course->title }}</strong></p>
    </div>

    <!-- Student details -->
    <div class="section">
        <div class="section-title">Informations Apprenant</div>
        <table class="info-table">
            <tr>
                <td style="width: 20%;"><strong>Nom complet :</strong></td>
                <td>{{ $student->full_name }}</td>
                <td style="width: 20%;"><strong>Progression :</strong></td>
                <td><strong>{{ round($progress_percentage) }}%</strong></td>
            </tr>
            <tr>
                <td><strong>Adresse Email :</strong></td>
                <td>{{ $student->email }}</td>
                <td><strong>Temps passé :</strong></td>
                <td>{{ $time_spent_minutes }} minutes</td>
            </tr>
            <tr>
                <td><strong>Inscription :</strong></td>
                <td>{{ $enrollment->created_at->format('d/m/Y') }}</td>
                <td><strong>Statut :</strong></td>
                <td>{{ strtoupper($enrollment->status) }}</td>
            </tr>
        </table>
    </div>

    <!-- Quizzes -->
    <div class="section">
        <div class="section-title">Scores aux Évaluations & Quiz</div>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Quiz / Examen</th>
                    <th>Score obtenu</th>
                    <th>Seuil de réussite</th>
                    <th>Statut</th>
                    <th>Date de remise</th>
                </tr>
            </thead>
            <tbody>
                @forelse($quiz_attempts as $attempt)
                    <tr>
                        <td>{{ $attempt->quiz->title }}</td>
                        <td>{{ $attempt->score }}%</td>
                        <td>{{ $attempt->quiz->passing_score }}%</td>
                        <td>{{ $attempt->passed ? 'Réussi' : 'Échoué' }}</td>
                        <td>{{ $attempt->created_at->format('d/m/Y H:i') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" style="text-align: center; color: #999;">Aucune tentative enregistrée.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Devoirs -->
    <div class="section">
        <div class="section-title">Résultats des Devoirs</div>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Intitulé du devoir</th>
                    <th>Statut</th>
                    <th>Note obtenue</th>
                    <th>Feedback général</th>
                </tr>
            </thead>
            <tbody>
                @forelse($submissions as $sub)
                    <tr>
                        <td>{{ $sub->assignment->title }}</td>
                        <td>{{ strtoupper($sub->status) }}</td>
                        <td>{{ $sub->score !== null ? $sub->score . ' / ' . $sub->assignment->max_score : '-' }}</td>
                        <td>{{ $sub->feedback ?? '-' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" style="text-align: center; color: #999;">Aucune soumission remise.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</body>
</html>
