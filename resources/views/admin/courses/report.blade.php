<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Rapport de formation : {{ $course->title }}</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #333;
            line-height: 1.5;
            margin: 20px;
        }
        h1 {
            color: #0052cc;
            font-size: 24px;
            margin-bottom: 5px;
            border-bottom: 2px solid #0052cc;
            padding-bottom: 10px;
        }
        .header-meta {
            font-size: 12px;
            color: #666;
            margin-bottom: 30px;
        }
        .section-title {
            font-size: 16px;
            font-weight: bold;
            color: #333;
            margin-top: 20px;
            margin-bottom: 10px;
            border-bottom: 1px solid #ddd;
            padding-bottom: 5px;
        }
        .kpi-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 25px;
        }
        .kpi-table td {
            width: 25%;
            padding: 15px;
            border: 1px solid #ddd;
            text-align: center;
        }
        .kpi-value {
            font-size: 20px;
            font-weight: bold;
            color: #0052cc;
            margin-bottom: 5px;
        }
        .kpi-label {
            font-size: 11px;
            color: #666;
            text-transform: uppercase;
        }
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        .data-table th, .data-table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
            font-size: 12px;
        }
        .data-table th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <h1>Rapport de formation — CabForm LMS</h1>
    <div class="header-meta">
        <strong>Formation :</strong> {{ $course->title }}<br>
        <strong>Instructeur :</strong> {{ $course->instructor->full_name ?? '-' }}<br>
        <strong>Date d'édition :</strong> {{ date('d/m/Y H:i') }}<br>
        <strong>Version :</strong> v{{ $course->version }}
    </div>

    <div class="section-title">Indicateurs clés de performance</div>
    <table class="kpi-table">
        <tr>
            <td>
                <div class="kpi-value">{{ $enrollmentsCount }}</div>
                <div class="kpi-label">Inscrits</div>
            </td>
            <td>
                <div class="kpi-value">{{ $completedCount }}</div>
                <div class="kpi-label">Complétés</div>
            </td>
            <td>
                <div class="kpi-value">{{ round($avgProgress, 1) }}%</div>
                <div class="kpi-label">Progression</div>
            </td>
            <td>
                <div class="kpi-value">{{ number_format($totalRevenue, 0, ',', ' ') }} XOF</div>
                <div class="kpi-label">Revenus</div>
            </td>
        </tr>
    </table>

    <div class="section-title">Description de la formation</div>
    <p style="font-size: 13px; color: #555;">
        {{ $course->description ?? 'Aucune description disponible pour cette formation.' }}
    </p>

    <div class="section-title">Détails financiers</div>
    <table class="data-table">
        <thead>
            <tr>
                <th>Paramètre</th>
                <th>Valeur</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Tarif Unitaire</td>
                <td>{{ number_format($course->price, 0, ',', ' ') }} XOF</td>
            </tr>
            <tr>
                <td>Total inscriptions payées</td>
                <td>{{ $enrollmentsCount }}</td>
            </tr>
            <tr>
                <td>Chiffre d'affaires total généré</td>
                <td style="font-weight: bold; color: #0052cc;">{{ number_format($totalRevenue, 0, ',', ' ') }} XOF</td>
            </tr>
        </tbody>
    </table>
</body>
</html>
