<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Supplier;
use Illuminate\Support\Str;

class SupplierFactory extends Factory
{
  /**
  * The name of the factory's corresponding model.
  *
  * @var string
  */
  protected $model = Supplier::class;
  
  /**
  * Define the model's default state.
  *
  * @return array
  */
  public function definition()
  {
    return [
      'name' => $this->faker->firstName,
      'address' => $this->faker->state,
      'contact' => $this->faker->e164phoneNumber,
      'email' => $this->faker->unique()->safeEmail
    ];
  }
}
