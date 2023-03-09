<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Damage;
use App\Models\Stock;
use App\User;

class DamageFactory extends Factory
{
  /**
   * The name of the factory's corresponding model.
   *
   * @var string
   */
  protected $model = Damage::class;

  /**
   * Define the model's default state.
   *
   * @return array
   */
  public function definition()
  {

    $item_id = Stock::inRandomOrder()->first()->id;
    $recorded_by = User::inRandomOrder()->first()->id;

    return [
      'item_id' => $item_id,
      'quantity' => $this->faker->numberBetween(3, 10),
      'recorded_by' => $recorded_by,
    ];
  }
}
