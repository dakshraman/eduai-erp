<?php

namespace Database\Factories;

use App\Models\SmPhoneCallLog;
use Illuminate\Database\Eloquent\Factories\Factory;

class SmPhoneCallLogFactory extends Factory
{
    protected $model = SmPhoneCallLog::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'phone' => $this->faker->numerify('##########'),
            'date' => $this->faker->date(),
            'description' => $this->faker->sentence(),
            'next_follow_up_date' => $this->faker->date(),
            'call_duration' => $this->faker->numberBetween(1, 60).'m',
            'call_type' => $this->faker->randomElement(['I', 'O']),
            'school_id' => 1,
            'academic_id' => 1,
        ];
    }
}


