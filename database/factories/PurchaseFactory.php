<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Purchase;
use App\Models\Stock;
use App\Models\Supplier;
use App\User;

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

    $item_id = Stock::inRandomOrder()->first()->id;
    $supplier_id = Supplier::inRandomOrder()->first()->id;
    $recorded_by = User::inRandomOrder()->first()->id;

    return [
      'item_id' => $item_id,
      'quantity' => $this->faker->numberBetween(10, 50),
      'cost_price_per_item' => $this->faker->numberBetween(4000, 6000),
      'retail_price' => $this->faker->numberBetween(2000, 10000),
      'supplier_id' => $supplier_id,
      'date' => $this->faker->date('Y-m-d', 'now'),
      'date_of_purchase' => $this->faker->date('Y-m-d', 'now'),
      'recorded_by' => $recorded_by,
    ];
  }
}
