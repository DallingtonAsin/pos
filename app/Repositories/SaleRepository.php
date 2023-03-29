<?php

namespace App\Repositories;

use App\Models\Sale;
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
            $order_number = date('YmdHis') . Str::uuid()->toString();
            return $order_number;
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

}
