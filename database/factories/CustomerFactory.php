<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Customer;
use App\User;

class CustomerFactory extends Factory
{
  /**
  * The name of the factory's corresponding model.
  *
  * @var string
  */
  protected $model = Customer::class;
  
  /**
  * Define the model's default state.
  *
  * @return array
  */
  public function definition()
  {

    $added_by = User::inRandomOrder()->first()->id;

    return [
      'name' => $this->faker->name,
      'contact' => $this->faker->unique()->phoneNumber,
      'address' => $this->faker->city,
      'added_by' => $added_by,
    ];
  }
}
