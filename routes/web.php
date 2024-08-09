<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/calendar', function () {
    return view('calendar');
});

use App\Http\Controllers\ScheduleController;

// イベント登録処理
Route::post('/schedule-add', [ScheduleController::class, 'scheduleAdd'])->name('schedule-add');
// イベント取得処理
Route::post('/schedule-get', [ScheduleController::class, 'scheduleGet'])->name('schedule-get');

//イベント更新処理
Route::post('/schedule-update', [ScheduleController::class, 'scheduleUpdate'])->name('schedule-update');

//イベント削除処理
Route::post('/schedule-delete', [ScheduleController::class, 'scheduleDelete'])->name('schedule-delete');
