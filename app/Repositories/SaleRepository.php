<?php

namespace App\Repositories;

use App\Models\Sale;
use Haruncpi\LaravelIdGenerator\IdGenerator;
use Illuminate\Support\Str;

class SaleRepository
{
    protected $sale;

    public function __construct(Sale $sale)
    {
        $this->sale = $sale;
    }

    public function create($saleData)
    {
        try {
            return $this->sale->create($saleData);
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    public function find($id)
    {
        try {
            return $this->sale->find($id);
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    public function get()
    {
        try {
            return $this->sale->get();
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    public function update($id, $saleData)
    {
        try {
            return $this->sale->where('id', $id)->update($saleData);
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    public function delete($id)
    {
        try {
            $sale = $this->sale->find($id);
            $sale->delete();
            return $sale;
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    public function count()
    {
        try {
            return $this->sale->count();
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    public function generateOrderNumber()
    {
        try {

            // $lastOrder = Sale::orderBy('id', 'desc')->first();
            // $newId = $lastOrder ? $lastOrder->id + 1 : 1;
            // $order_number = 'SO' . str_pad($newId, 5, '0', STR_PAD_LEFT);
            // $order_number = $this->generateUniqueNumber('sales', 'order_number', 12, 'SO');
            $order_number = date('YmdHis') . Str::uuid()->toString();
            dd($order_number);
            return $order_number;
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    private function generateUniqueNumber($table, $column = null, $length, $prefix)
    {
      try {
  
        $config = [
          'table' => $table,
          'length' => $length,
          'prefix' => $prefix
        ];
  
        if ($column != null) {
          $config['field'] = $column;
        }
  
        $order_number = IdGenerator::generate($config);
        return $order_number;
      } catch (\Exception $ex) {
        throw $ex;
      }
    }
}
