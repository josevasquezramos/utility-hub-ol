<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Cronograma</title>
    
    @php
        $thFontSize = 16;
        $tdFontSize = 16;
        $h1FontSize = 26;
        $tdPaddingY = 30;
        $thPadding  = 15;

        if ($perPage > 5) {
            $diff = $perPage - 5;
            $thFontSize = max(9, round(16 - ($diff * 1.5)));
            $tdFontSize = max(9, round(16 - ($diff * 1.5)));
            $h1FontSize = max(18, round(26 - ($diff * 1.2)));
            $tdPaddingY = max(5, round(30 - ($diff * 4.5)));
            $thPadding  = max(8, round(15 - ($diff * 1.5)));
        } elseif ($perPage < 5) {
            $diff = 5 - $perPage;
            $thFontSize = min(18, round(16 + ($diff * 1)));
            $tdFontSize = min(18, round(16 + ($diff * 1)));
            $tdPaddingY = min(40, round(30 + ($diff * 5)));
        }
    @endphp

    <style>
        @page {
            size: A4 landscape;
            margin: 2cm;
        }

        body {
            font-family: 'Segoe UI', 'Verdana', sans-serif;
            margin: 0;
            padding: 0;
            color: #333;
        }

        h1 {
            text-align: center;
            font-size: {{ $h1FontSize }}pt;
            color: #1B4F72;
            text-transform: uppercase;
            font-weight: bolder;
            margin-top: 0;
            margin-bottom: {{ $tdPaddingY + 10 }}px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            border: 2px solid #1B4F72;
        }

        th {
            background-color: #2874A6;
            color: #ffffff !important;
            font-size: {{ $thFontSize }}pt;
            text-transform: uppercase;
            font-weight: bold;
            padding: {{ $thPadding }}px;
            border: 2px solid #1B4F72;
        }

        td {
            border: 2px solid #1B4F72;
            text-align: center;
            vertical-align: middle;
            font-size: {{ $tdFontSize }}pt;
            color: #1B4F72;
            padding: {{ $tdPaddingY }}px 10px; 
        }

        .col-day {
            background-color: #ccdfea;
            color: #1B4F72;
            font-weight: bold;
            text-align: left;
            padding-left: 20px;
            width: 20%;
            border-right: 1px solid #1B4F72;
        }

        .date-text {
            color: #1B4F72;
            font-weight: bold;
            font-size: {{ $tdFontSize }}pt;
        }

        .page-break {
            page-break-after: always;
        }
    </style>
</head>
<body>

    @foreach($chunks as $pageIndex => $currentAssignments)
        
        @php
            $uniqueDates = $currentAssignments->pluck('date')->unique()->sort();
        @endphp

        <h1>Cronograma: {{ $activity->name }}</h1>

        <table>
            <thead>
                <tr>
                    <th class="col-day" style="background-color: #2874A6; color: #ffffff; width: 7rem; text-transform: none;">Día</th>
                    @foreach($currentAssignments as $assignment)
                        <th>{{ $assignment->assignee_name }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach($uniqueDates as $date)
                    @php
                        $dateObj = \Carbon\Carbon::parse($date);
                    @endphp
                    
                    <tr>
                        <td class="col-day">
                            {{ ucfirst($dateObj->locale('es')->dayName) }}
                        </td>

                        @foreach($currentAssignments as $assignment)
                            <td>
                                @if($assignment->date == $date)
                                    <span class="date-text">
                                        {{ \Carbon\Carbon::parse($assignment->date)->format('d/m/Y') }}
                                    </span>
                                @endif
                            </td>
                        @endforeach
                    </tr>
                @endforeach
            </tbody>
        </table>

        @if(!$loop->last)
            <div class="page-break"></div>
        @endif

    @endforeach

</body>
</html>