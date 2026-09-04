<?php

namespace Database\Factories;

use App\Models\SmAdmissionQueryFollowup;
use Illuminate\Database\Eloquent\Factories\Factory;

class SmAdmissionQueryFollowupFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = SmAdmissionQueryFollowup::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'response' => $this->faker->sentence($nbWords = 3, $variableNbWords = true),
            'note' => $this->faker->sentence($nbWords = 4, $variableNbWords = true),
            'date' => $this->faker->dateTime()->format('Y-m-d'),
            'admission_query_id' => 1,
            'school_id' => 1,
            'academic_id' => 1,
            'created_by' => 1,
        ];
    }
}
