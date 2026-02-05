<?php

namespace App\Exports;

use App\Models\Activity;
use App\Models\Assignment;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ScheduleExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    protected $start;
    protected $end;
    protected $activities;
    protected $assignmentsMap;

    public function __construct($start, $end)
    {
        $this->start = Carbon::parse($start);
        $this->end = Carbon::parse($end);
        
        $this->activities = Activity::orderBy('id')->get();
        
        $assignments = Assignment::whereBetween('date', [$this->start, $this->end])->get();
        
        $this->assignmentsMap = $assignments->mapWithKeys(function ($item) {
            return [$item->date . '_' . $item->activity_id => $item->assignee_name];
        });
    }

    /**
    * La colección base son los DÍAS del rango seleccionado
    */
    public function collection()
    {
        $period = CarbonPeriod::create($this->start, $this->end);
        $days = [];

        foreach ($period as $date) {
            $days[] = $date;
        }

        return collect($days);
    }

    /**
    * Definimos los encabezados de la tabla
    */
    public function headings(): array
    {
        $headers = ['Día', 'Fecha'];

        foreach ($this->activities as $activity) {
            $headers[] = $activity->name;
        }

        return $headers;
    }

    /**
    * Mapeamos cada fila (cada día)
    * @var Carbon $date
    */
    public function map($date): array
    {
        $row = [
            ucfirst($date->locale('es')->dayName),
            $date->format('d/m/Y'),
        ];

        foreach ($this->activities as $activity) {
            $key = $date->format('Y-m-d') . '_' . $activity->id;
            $row[] = $this->assignmentsMap[$key] ?? '-';
        }

        return $row;
    }

    /**
    * Estilos opcionales (Negrita en encabezados)
    */
    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}