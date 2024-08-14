<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Schedule;
use Carbon\Carbon;

class ScheduleController extends Controller
{
    public function scheduleAdd(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date_format:Y-m-d\TH:i',
            'end_date' => 'required|date_format:Y-m-d\TH:i',
            'event_name' => 'required|max:32',
            'location' => 'nullable|string|max:255',
            'link' => 'nullable|url|max:255',
            'memo' => 'nullable|string|max:255',
        ]);

        // 送信された日時をCarbonで処理し、適切な形式に変換
        $start_date = Carbon::createFromFormat('Y-m-d\TH:i', $request->input('start_date'))->format('Y-m-d H:i:s');
        $end_date = Carbon::createFromFormat('Y-m-d\TH:i', $request->input('end_date'))->format('Y-m-d H:i:s');

        // デバッグメッセージを出力して確認
        if (!$start_date || !$end_date) {
            return response()->json(['error' => 'Invalid date format'], 400);
        }

        $schedule = new Schedule;
        $schedule->start_date = $start_date;
        $schedule->end_date = $end_date;
        $schedule->event_name = $request->input('event_name');
        $schedule->location = $request->input('location');
        $schedule->link = $request->input('link');
        $schedule->memo = $request->input('memo');
        $schedule->save();

        return response()->json(['id' => $schedule->id, 'status' => 'success']);
    }

    public function scheduleDropUpdate(Request $request)
    {
        $request->validate([
            'id' => 'required|integer',
            'start_date' => 'required|date_format:Y-m-d\TH:i',
            'end_date' => 'required|date_format:Y-m-d\TH:i',
            'event_name' => 'required|max:32',
            'location' => 'nullable|string|max:255',
            'link' => 'nullable|url|max:255',
            'memo' => 'nullable|string|max:255',
        ]);

        // 送信された日時をCarbonで処理し、適切な形式に変換
        $start_date = Carbon::createFromFormat('Y-m-d\TH:i', $request->input('start_date'))->format('Y-m-d H:i:s');
        $end_date = Carbon::createFromFormat('Y-m-d\TH:i', $request->input('end_date'))->format('Y-m-d H:i:s');

        // デバッグメッセージを出力して確認
        if (!$start_date || !$end_date) {
            return redirect('/calendar')->with('error', 'Invalid date format');
        }

        $schedule = Schedule::find($request->input('id'));
        if ($schedule) {
            $schedule->start_date = $start_date;
            $schedule->end_date = $end_date;
            $schedule->event_name = $request->input('event_name');
            $schedule->location = $request->input('location');
            $schedule->link = $request->input('link');
            $schedule->memo = $request->input('memo');
            $schedule->save();

            return redirect('/calendar')->with('success', 'イベントが更新されました');
        }

        return redirect('/calendar')->with('error', 'イベントが見つかりませんでした');
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

    public function scheduleDelete(Request $request)
    {
        $request->validate([
            'id' => 'required|integer',
        ]);

        $schedule = Schedule::find($request->input('id'));
        if ($schedule) {
            $schedule->delete();
            return response()->json(['status' => 'success']);
        }

        return response()->json(['status' => 'error', 'message' => 'スケジュールが見つかりませんでした。'], 404);
    }

    public function showEditForm($id)
    {
        $event = Schedule::find($id);
        if ($event) {
            return view('schedule-edit-form', ['event' => $event]);
        }
        return redirect('/calendar')->with('error', 'イベントが見つかりませんでした');
    }
}
