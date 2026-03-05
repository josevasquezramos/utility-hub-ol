<?php

namespace App\Http\Controllers;

use App\Exports\ScheduleExport;
use App\Models\Activity;
use App\Models\Assignment;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Str;

class ScheduleExportController extends Controller
{
    public function download(Request $request)
    {
        $request->validate([
            'activity_id' => 'required|exists:activities,id',
            'date_range' => 'required|string',
        ]);

        $dates = preg_split('/ (to|a) /', $request->date_range);
        $start = Carbon::parse($dates[0]);
        $end = count($dates) > 1 ? Carbon::parse($dates[1]) : $start;

        $activity = Activity::findOrFail($request->activity_id);

        $assignments = Assignment::where('activity_id', $activity->id)
            ->whereBetween('date', [$start, $end])
            ->whereNotNull('assignee_name')
            ->where('assignee_name', '!=', '')
            ->orderBy('date')
            ->get();

        if ($assignments->isEmpty()) {
            return response('<div style="font-family:sans-serif; text-align:center; margin-top:50px;">No hay datos para generar el PDF.<br><button onclick="window.close()" style="margin-top:30px;">Cerrar</button></div>');
        }

        $chunks = $assignments->chunk(5);

        $pdf = Pdf::loadView('schedule.pdf_per_activity', [
            'activity' => $activity,
            'chunks' => $chunks
        ]);

        $pdf->setPaper('a4', 'landscape');

        $formattedStart = $start->format('d-m-Y');
        $formattedEnd = $end->format('d-m-Y');

        if ($start->ne($end)) {
            $fileName = "Cronograma {$activity->name} {$formattedStart} {$formattedEnd}.pdf";
        } else {
            $fileName = "Cronograma {$activity->name} {$formattedStart}.pdf";
        }

        return $pdf->stream($fileName);
    }

    public function downloadExcel(Request $request)
    {
        $request->validate([
            'date_range' => 'required|string',
        ]);

        $dates = preg_split('/ (to|a) /', $request->date_range);
        $start = Carbon::parse($dates[0]);
        $end = count($dates) > 1 ? Carbon::parse($dates[1]) : $start;

        return Excel::download(new ScheduleExport($start, $end), 'reporte_cronograma.xlsx');
    }
}