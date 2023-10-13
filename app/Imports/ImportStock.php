<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use App\Models\Stock;

class ImportStock implements ToModel, WithHeadingRow
{
  /**
   * @param array $row
   *
   * @return \Illuminate\Database\Eloquent\Model|null
   */
  public function model(array $row)
  {

    $item_code = $row['barcode'];
    $item = $row['item'];

    (isset($item_code))
      ? $item_code = $item_code
      : $item_code = null;

    if (Stock::where('item_code', $item_code)->exists()) {
      $qty = $this->getItemQty($item);
      $newQty = $qty + floatval($row['quantity']);
      Stock::where('item_code', $item_code)->update(['quantity' => $newQty]);
    } else {

      $whereClause  = [
        'item_code' => $row['barcode']
      ];

      return Stock::updateOrCreate($whereClause, [
        'item_code' => $item_code,
        'item' => $item,
        'quantity' => floatval($row['quantity'] ? $row['quantity'] : 0),
        'buying_price' => floatval($row['costprice'] ? $row['costprice'] : 0),
        'selling_price' => floatval($row['sellingprice'] ? $row['sellingprice'] : 0),
        'wholesale_price' => floatval($row['wholesaleprice'] ? $row['wholesaleprice'] : 0),
        'supplier' => $row['supplier']
      ]);
    }
  }


  protected function getStock()
  {

    $items = Stock::all();
    $itemsArr = array();
    foreach ($items as $item) {
      array_push($itemsArr, $item->item);
    }

    return $itemsArr;
  }


  protected function getItemQty($item)
  {

    $qty = Stock::where('item', $item)
      ->value('quantity');
    return $qty;
  }
}