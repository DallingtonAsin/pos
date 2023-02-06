<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Purchase;
use Illuminate\Support\Str;

class PurchaseFactory extends Factory
{
  /**
  * The name of the factory's corresponding model.
  *
  * @var string
  */
  protected $model = Purchase::class;
  
  /**
  * Define the model's default state.
  *
  * @return array
  */
  public function definition()
  {
    return [
        'item_code' => Str::random(3),
        'item' => Str::random(7),
        'quantity' => $this->faker->randomDigitNot(0),
        'cost_price_per_item' => $this->faker->numberBetween($min=4000, $max=6000),
        'supplier' => $this->faker->lastName,
        'date' => $this->faker->date($format='Y-m-d', $max='now'),
        'date_of_purchase' => $this->faker->date($format='Y-m-d', $max='now'),
        'recorded_by' => $this->faker->firstName,
    ];
  }
}
