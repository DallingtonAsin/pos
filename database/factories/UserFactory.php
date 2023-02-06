<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class UserFactory extends Factory
{
  /**
  * The name of the factory's corresponding model.
  *
  * @var string
  */
  protected $model = User::class;
  
  /**
  * Define the model's default state.
  *
  * @return array
  */
  public function definition()
  {
    return [
      'first_name' => $this->faker->firstName,
      'last_name' => $this->faker->lastName,
      'name' => $this->faker->name,
      'username' => $this->faker->unique()->lastName,
      'gender' => 'Male',
      'email' => $this->faker->unique()->safeEmail,
      'user_role' => $this->faker->randomElement([1, 2]),
      'tel_no' => $this->faker->phoneNumber,
      'alt_telno' => $this->faker->phoneNumber,
      'address' => $this->faker->state,
      'nationalID_no' => strtoupper(Str::random(14)),
      'email_verified_at' => now(),
      'image' => NULL,
      'password' => Hash::make('admin@123'),
      'remember_token' => Str::random(10),
    ];
  }
}
