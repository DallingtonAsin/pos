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
      'item_code' => Str::random(3),
      'item' => Str::random(7),
      'category_id' => $category_id,
      'quantity' => $this->faker->randomDigit,
      'threshold_qty' => $this->faker->randomDigit,
      'buying_price' => $this->faker->numberBetween($min = 4000, $max = 6000),
      'selling_price' => $this->faker->numberBetween($min = 6000, $max = 9000),
      'supplier_id' => $supplier_id,
      'date_of_entry' => $this->faker->dateTimeThisYear($max = 'now', $timezone = null),
      'expiry_date' => $this->faker->dateTimeThisYear($max = 'now', $timezone = null),
    ];
  }
}
