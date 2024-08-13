<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Schedule;

class ScheduleController extends Controller
{
    public function scheduleAdd(Request $request)
    {
        $request->validate([
            'start_date' => 'required|integer',
            'end_date' => 'required|integer',
            'event_name' => 'required|max:32',
            'location' => 'nullable|string|max:255',
            'link' => 'nullable|url|max:255',
            'memo' => 'nullable|string|max:255',
        ]);

        $schedule = new Schedule;
        $schedule->start_date = date('Y-m-d H:i:s', $request->input('start_date') / 1000);
        $schedule->end_date = date('Y-m-d H:i:s', $request->input('end_date') / 1000);
        $schedule->event_name = $request->input('event_name');
        $schedule->location = $request->input('location');
        $schedule->link = $request->input('link');
        $schedule->memo = $request->input('memo');
        $schedule->save();

        return response()->json(['id' => $schedule->id, 'status' => 'success']);
    }

    public function scheduleGet(Request $request)
    {
        $request->validate([
            'start_date' => 'required|integer',
            'end_date' => 'required|integer'
        ]);

        $start_date = date('Y-m-d H:i:s', $request->input('start_date') / 1000);
        $end_date = date('Y-m-d H:i:s', $request->input('end_date') / 1000);

        $events = Schedule::query()
            ->select(
                'id',
                'start_date as start',
                'end_date as end',
                'event_name as title',
                'location',
                'link',
                'memo'
            )
            ->where('end_date', '>', $start_date)
            ->where('start_date', '<', $end_date)
            ->get();

        return response()->json($events);
    }

    public function scheduleUpdate(Request $request)
    {
        $request->validate([
            'id' => 'required|integer',
            'start_date' => 'required|integer',
            'end_date' => 'required|integer',
            'event_name' => 'required|max:32',
            'location' => 'nullable|string|max:255',
            'link' => 'nullable|url|max:255',
            'memo' => 'nullable|string|max:255',
        ]);

        $schedule = Schedule::find($request->input('id'));
        if ($schedule) {
            $schedule->start_date = date('Y-m-d H:i:s', $request->input('start_date') / 1000);
            $schedule->end_date = date('Y-m-d H:i:s', $request->input('end_date') / 1000);
            $schedule->event_name = $request->input('event_name');
            $schedule->location = $request->input('location');
            $schedule->link = $request->input('link');
            $schedule->memo = $request->input('memo');
            $schedule->save();

            return response()->json(['status' => 'success']);
        }

        return response()->json(['status' => 'error', 'message' => 'スケジュールが見つかりませんでした。'], 404);
    }

    public function scheduleDelete(Request $request)
    {
        $request->validate([
            'id' => 'required|integer',
        ]);

        $schedule = Schedule::find($request->input('id'));
        if ($schedule) {
            $schedule->delete();
        }

        return response()->json(['status' => 'success']);
    }
}
