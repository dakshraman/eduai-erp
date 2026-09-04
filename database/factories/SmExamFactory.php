<?php

namespace Database\Factories;

use App\Models\SmExam;
use App\Models\SmClass;
use App\Models\SmSection;
use App\Models\SmSubject;
use App\Models\SmExamType;
use Illuminate\Database\Eloquent\Factories\Factory;

class SmExamFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = SmExam::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            "parent_id" => 0,
            "exam_mark" => $this->faker->numberBetween(50, 100),
            "pass_mark" => $this->faker->numberBetween(30, 50),
            "active_status" => 1,
            "exam_type_id" => SmExamType::factory(),
            "class_id" => SmClass::factory(),
            "section_id" => SmSection::factory(),
            "subject_id" => SmSubject::factory(),
            "created_by" => 1,
            "updated_by" => 1,
            "school_id" => 1,
            "academic_id" => 1,
            "created_at" => now(),
            "updated_at" => now(),
        ];
    }

    /**
     * Indicate that the exam is inactive.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function inactive()
    {
        return $this->state(function (array $attributes) {
            return [
                "active_status" => 0,
            ];
        });
    }

    /**
     * Indicate that the exam is for a specific school.
     *
     * @param int $schoolId
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function forSchool($schoolId)
    {
        return $this->state(function (array $attributes) use ($schoolId) {
            return [
                "school_id" => $schoolId,
            ];
        });
    }

    /**
     * Indicate that the exam is for a specific academic year.
     *
     * @param int $academicId
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function forAcademicYear($academicId)
    {
        return $this->state(function (array $attributes) use ($academicId) {
            return [
                "academic_id" => $academicId,
            ];
        });
    }
}
