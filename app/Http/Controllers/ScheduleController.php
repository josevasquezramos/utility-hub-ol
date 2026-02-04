<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Assignment;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;

class ScheduleController extends Controller
{
    public function index(Request $request)
    {
        $date = $request->input('date') ? Carbon::parse($request->input('date')) : Carbon::now();
        $startOfMonth = $date->copy()->startOfMonth();
        $endOfMonth = $date->copy()->endOfMonth();

        $activities = Activity::all();
        $period = CarbonPeriod::create($startOfMonth, $endOfMonth);

        $assignments = Assignment::whereBetween('date', [$startOfMonth, $endOfMonth])
            ->get(['date', 'activity_id', 'assignee_name'])
            ->mapWithKeys(function ($item) {
                return [$item->date . '_' . $item->activity_id => $item->assignee_name];
            });

        $holidays = ['01-01', '01-05', '29-06', '28-07', '29-07', '30-08', '08-10', '01-11', '08-12', '25-12'];

        return view('schedule.index', compact('activities', 'period', 'assignments', 'date', 'holidays'));
    }

    public function storeActivity(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        Activity::create($validated);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Actividad creada exitosamente.'
            ]);
        }

        return back()->with('success', 'Actividad creada exitosamente.');
    }

    public function deleteActivity(Request $request, Activity $activity)
    {
        $hasAssignments = $activity->assignments()
            ->whereNotNull('assignee_name')
            ->where('assignee_name', '!=', '')
            ->exists();

        if ($hasAssignments) {
            $message = 'No puedes eliminar esta actividad porque tiene personas asignadas.';

            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => $message], 422);
            }
            return back()->withErrors(['msg' => $message]);
        }

        $activity->delete();

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Actividad eliminada exitosamente.'
            ]);
        }

        return back()->with('success', 'Actividad eliminada exitosamente.');
    }

    public function updateAssignment(Request $request)
    {
        $data = $request->validate([
            'date' => 'required|date',
            'activity_id' => 'required|exists:activities,id',
            'assignee_name' => 'nullable|string|max:100'
        ]);

        Assignment::updateOrCreate(
            ['date' => $data['date'], 'activity_id' => $data['activity_id']],
            ['assignee_name' => $data['assignee_name']]
        );

        return response()->json(['status' => 'saved']);
    }
}