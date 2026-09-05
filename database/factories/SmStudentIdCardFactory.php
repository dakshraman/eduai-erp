<?php

namespace Database\Factories;

use App\Models\SmStudentIdCard;
use Illuminate\Database\Eloquent\Factories\Factory;

class SmStudentIdCardFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = SmStudentIdCard::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'title' => $this->faker->words(2, true),
            'logo' => 'public/backEnd/id_card/img/logo.png',
            'signature' => 'public/backEnd/id_card/img/Signature.png',
            'background_img' => 'public/backEnd/id_card/img/vertical_bg.png',
            'profile_image' => 'public/backEnd/id_card/img/thumb.png',
            'role_id' => json_encode([2]),
            'page_layout_style' => 'v',
            'user_photo_style' => 'round',
            'user_photo_width' => '120',
            'user_photo_height' => '120',
            'pl_width' => 300,
            'pl_height' => 500,
            't_space' => 10,
            'b_space' => 10,
            'r_space' => 10,
            'l_space' => 10,
            'admission_no' => 1,
            'student_name' => 1,
            'class' => 1,
            'father_name' => 1,
            'mother_name' => 0,
            'student_address' => 1,
            'phone_number' => 0,
            'dob' => 1,
            'blood' => 1,
            'photo' => 1,
            'signature_status' => 0,
            'staff_department' => 0,
            'staff_designation' => 0,
            'active_status' => 1,
            'school_id' => 1,
            'academic_id' => 1,
        ];
    }
}
