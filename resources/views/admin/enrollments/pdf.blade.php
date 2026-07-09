<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Liste des Apprenants Inscrits</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #333;
            line-height: 1.5;
            margin: 20px;
        }
        h1 {
            color: #0052cc;
            font-size: 22px;
            margin-bottom: 5px;
            border-bottom: 2px solid #0052cc;
            padding-bottom: 10px;
        }
        .header-meta {
            font-size: 11px;
            color: #666;
            margin-bottom: 25px;
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
            font-size: 11px;
        }
        .data-table th {
            background-color: #f2f2f2;
            font-weight: bold;
            color: #333;
        }
        .status-badge {
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 10px;
            font-weight: bold;
            display: inline-block;
        }
        .status-active {
            background-color: #d4edda;
            color: #155724;
        }
        .status-completed {
            background-color: #cce5ff;
            color: #004085;
        }
        .status-suspended {
            background-color: #f8d7da;
            color: #721c24;
        }
    </style>
</head>
<body>
    <h1>Rapport des Apprenants Inscrits — CabForm LMS</h1>
    <div class="header-meta">
        <strong>Généré le :</strong> {{ date('d/m/Y H:i') }}<br>
        <strong>Nombre d'inscriptions :</strong> {{ count($enrollments) }}
    </div>

    <table class="data-table">
        <thead>
            <tr>
                <th width="5%">ID</th>
                <th width="25%">Apprenant</th>
                <th width="25%">Email</th>
                <th width="25%">Formation</th>
                <th width="10%">Progression</th>
                <th width="10%">Statut</th>
            </tr>
        </thead>
        <tbody>
            @forelse($enrollments as $e)
            <tr>
                <td>{{ $e->id }}</td>
                <td><strong>{{ $e->user->full_name ?? '-' }}</strong></td>
                <td>{{ $e->user->email ?? '-' }}</td>
                <td>{{ $e->course->title ?? '-' }}</td>
                <td style="text-align: center;">{{ number_format($e->progress_percentage, 0) }}%</td>
                <td style="text-align: center;">
                    @if($e->status === 'active')
                        <span class="status-badge status-active">Actif</span>
                    @elseif($e->status === 'completed')
                        <span class="status-badge status-completed">Complété</span>
                    @else
                        <span class="status-badge status-suspended">Suspendu</span>
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" style="text-align: center; color: #666; padding: 20px;">Aucun inscrit ne correspond aux critères.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
