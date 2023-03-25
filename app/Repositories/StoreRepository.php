<?php

namespace App\Repositories;

use App\Models\Store;

class StoreRepository
{
    protected $store;

    public function __construct(Store $store)
    {
        $this->store = $store;
    }

    public function create($storeData)
    {
        try {
            return $this->store->create($storeData);
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    public function find($id)
    {
        try {
            return $this->store->find($id);
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    public function get()
    {
        try {
            return $this->store->select(['id', 'name', 'is_deleted', 'added_by'])->get();
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    public function update($id, $storeData)
    {
        try {
            return $this->store->where('id', $id)->update($storeData);
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    public function delete($id)
    {
        try {
            $store = $this->store->find($id);
            $store->delete();
            return $store;
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    public function count()
    {
        try {
            return $this->store->where('is_deleted', false)->count();
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    public function value()
    {
        try {
            return $this->store->where('is_deleted', false)->sum('amount');
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    public function exists($name)
    {
        try {
            return $this->store->where('name', $name)->exists();
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    public function existsonUpdate($id, $name)
    {
        try {
            return $this->store->where('id', '!=', $id)->where('name', $name)->exists();
        } catch (\Exception $ex) {
            throw $ex;
        }
    }
}
