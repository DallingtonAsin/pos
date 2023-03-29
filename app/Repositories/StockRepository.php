<?php

namespace App\Repositories;

use App\Models\Stock;

class StockRepository
{
    protected $stock;

    public function __construct(Stock $stock)
    {
        $this->stock = $stock;
    }

    public function create($stockData)
    {
        try {
            return $this->stock->create($stockData);
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    public function find($id)
    {
        try {
            return $this->stock->find($id);
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    public function get()
    {
        try {
            return $this->stock->get();
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    public function update($id, $stockData)
    {
        try {
            return $this->stock->where('id', $id)->update($stockData);
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    public function delete($id)
    {
        try {
            $stock = $this->stock->find($id);
            $stock->delete();
            return $stock;
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    public function count()
    {
        try {
            return $this->stock->count();
        } catch (\Exception $ex) {
            throw $ex;
        }
    }


    public function exists($name)
    {
        try {
            return $this->stock->where('name', $name)->exists();
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    public function existsonUpdate($id, $name, $contact)
    {
        try {
            return $this->stock->where('id', '!=', $id)->where('name', $name)->exists();
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    public function getItemByCode($item_id)
    {
        return $this->stock->where('item_code', $item_id)->get();
    }

    public function getItemByName($item_name)
    {
        return $this->stock->where('item', $item_name)->get();
    }

    public function updateByItemName($item, $stockData)
    {
        try {
            return $this->stock->where('item', $item)->update($stockData);
        } catch (\Exception $ex) {
            throw $ex;
        }
    }
}
