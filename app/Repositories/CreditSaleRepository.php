<?php

namespace App\Repositories;

use App\Models\CreditSale;

class CreditSaleRepository
{
    protected $creditSale;

    public function __construct(CreditSale $creditSale)
    {
        $this->creditSale = $creditSale;
    }

    public function create($creditSaleData)
    {
        try {
            return $this->creditSale->create($creditSaleData);
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    public function find($id)
    {
        try {
            return $this->creditSale->find($id);
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    public function get()
    {
        try {
            return $this->creditSale->get();
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    public function update($id, $creditSaleData)
    {
        try {
            return $this->creditSale->where('id', $id)->update($creditSaleData);
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    public function delete($id)
    {
        try {
            $creditSale = $this->creditSale->find($id);
            $creditSale->delete();
            return $creditSale;
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    public function count()
    {
        try {
            return $this->creditSale->count();
        } catch (\Exception $ex) {
            throw $ex;
        }
    }
}
