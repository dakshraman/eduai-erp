<?php

namespace Database\Factories;

use App\Models\SmExamScheduleSubject;
use App\Models\SmExamSchedule;
use App\Models\SmSubject;
use Illuminate\Database\Eloquent\Factories\Factory;

class SmExamScheduleSubjectFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = SmExamScheduleSubject::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'exam_schedule_id' => SmExamSchedule::factory(),
            'subject_id' => SmSubject::factory(),
            'date' => $this->faker->dateTimeBetween('now', '+3 months')->format('Y-m-d'),
            'start_time' => $this->faker->time('H:i'),
            'end_time' => $this->faker->time('H:i'),
            'room' => $this->faker->randomElement(['Room A', 'Room B', 'Room C', 'Hall 1', 'Hall 2']),
            'full_mark' => $this->faker->numberBetween(50, 100),
            'pass_mark' => $this->faker->numberBetween(30, 50),
            'school_id' => 1,
            'academic_id' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    /**
     * Indicate that the exam schedule subject is for a specific school.
     *
     * @param int $schoolId
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function forSchool($schoolId)
    {
        return $this->state(function (array $attributes) use ($schoolId) {
            return [
                'school_id' => $schoolId,
            ];
        });
    }

    /**
     * Indicate that the exam schedule subject is for a specific academic year.
     *
     * @param int $academicId
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function forAcademicYear($academicId)
    {
        return $this->state(function (array $attributes) use ($academicId) {
            return [
                'academic_id' => $academicId,
            ];
        });
    }
}
