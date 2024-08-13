<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Schedule;

class ScheduleSeeder extends Seeder
{
    public function run()
    {
        // 10件のランダムなスケジュールを作成
        Schedule::factory()->count(10)->create();
    }
}
