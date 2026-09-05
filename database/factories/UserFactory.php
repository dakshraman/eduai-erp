<?php

namespace Database\Factories;

use App\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

class UserFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public $roles = [4, 5, 6, 7, 8, 9];

    public $i = 0;

    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = User::class;

    public function definition()
    {
        static $userIndex = 0;

        $userIndex++;

        return [
            'full_name' => $this->faker->firstNameMale ?? $this->faker->firstNameFemale,
            'email' => 'user_'.$userIndex.'@infixedu.com',
            'username' => 'user_'.$userIndex.'@infixedu.com',
            'role_id' => $this->faker->numberBetween(4, 9),
            'is_administrator' => 'no',
            'password' => Hash::make('123456'),
            // Include commonly-needed columns so factory()->create([...]) overrides
            // work even though these are not in User::$fillable.
            // The framework's Model::unguarded() context allows setting them.
            'active_status' => 1,
            'access_status' => 1,
            'school_id'     => 1,
        ];
    }
}
