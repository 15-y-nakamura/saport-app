<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Schedule;

class ScheduleFactory extends Factory
{
    protected $model = Schedule::class;

    public function definition()
    {
        return [
            'start_date' => $this->faker->dateTimeBetween('-1 month', '+1 month'),
            'end_date' => $this->faker->dateTimeBetween('+1 day', '+2 months'),
            'event_name' => $this->faker->sentence(3),
            'location' => $this->faker->address,
            'link' => $this->faker->url,
            'memo' => $this->faker->text(50),
        ];
    }
}
