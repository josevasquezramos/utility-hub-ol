<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Cronograma</title>
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
            font-size: 26pt;
            color: #1B4F72;
            text-transform: uppercase;
            font-weight: bolder;
            margin-top: 0;
            margin-bottom: 50px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            border: 2px solid #1B4F72;
        }

        th {
            background-color: #2874A6;
            color: #ffffff !important;
            font-size: 16pt;
            text-transform: uppercase;
            font-weight: bold;
            padding: 15px;
            border: 2px solid #1B4F72;
        }

        td {
            border: 2px solid #1B4F72;
            text-align: center;
            vertical-align: middle;
            font-size: 16pt;
            color: #1B4F72;
            padding: 30px 10px; 
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
            font-size: 16pt;
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