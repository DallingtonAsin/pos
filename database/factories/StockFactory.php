<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Stock;
use App\Models\StockCat;
use App\Models\Supplier;
use Illuminate\Support\Str;

class StockFactory extends Factory
{
  /**
   * The name of the factory's corresponding model.
   *
   * @var string
   */
  protected $model = Stock::class;

  /**
   * Define the model's default state.
   *
   * @return array
   */
  public function definition()
  {

    $supplier_id = Supplier::inRandomOrder()->first()->id;
    $category_id = StockCat::inRandomOrder()->first()->id;

    return [
      'item_code' => $this->faker->numberBetween(10000000000, 90000000000),
      'item' => Str::random(7),
      'category_id' => $category_id,
      'quantity' => $this->faker->numberBetween(150, 300),
      'threshold_qty' => $this->faker->numberBetween(5, 15),
      'buying_price' => $this->faker->numberBetween(4000,6000),
      'selling_price' => $this->faker->numberBetween(6000,9000),
      'supplier_id' => $supplier_id,
      'date_of_entry' => $this->faker->dateTimeThisYear('now', 'Africa/Kampala'),
      'expiry_date' => $this->faker->dateTimeThisYear('now', 'Africa/Kampala'),
    ];
  }
}
