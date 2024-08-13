<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ScheduleController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/calendar', function () {
    return view('calendar');
});

// イベント登録処理
Route::post('/schedule-add', [ScheduleController::class, 'scheduleAdd'])->name('schedule-add');
// イベント取得処理
Route::post('/schedule-get', [ScheduleController::class, 'scheduleGet'])->name('schedule-get');
// イベント更新処理
Route::post('/schedule-update', [ScheduleController::class, 'scheduleUpdate'])->name('schedule-update');
// イベント削除処理
Route::post('/schedule-delete', [ScheduleController::class, 'scheduleDelete'])->name('schedule-delete');

// イベント追加フォームの表示
Route::get('/schedule-add-form', function () {
    return view('schedule-add-form');
})->name('schedule-add-form');
