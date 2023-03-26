<?php

namespace App\Repositories;

use App\Models\Supplier;

class SupplierRepository
{
    protected $supplier;

    public function __construct(Supplier $supplier)
    {
        $this->supplier = $supplier;
    }

    public function create($supplierData)
    {
        try {
            return $this->supplier->create($supplierData);
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    public function find($id)
    {
        try {
            return $this->supplier->find($id);
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    public function get()
    {
        try {
            return $this->supplier->get();
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    public function update($id, $supplierData)
    {
        try {
            return $this->supplier->where('id', $id)->update($supplierData);
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    public function delete($id)
    {
        try {
            $supplier = $this->supplier->find($id);
            $supplier->delete();
            return $supplier;
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    public function count()
    {
        try {
            return $this->supplier->count();
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    public function exists($name)
    {
        try {
            return $this->supplier->where('name', $name)->exists();
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    public function existsonUpdate($id, $name)
    {
        try {
            return $this->supplier->where('id', '!=', $id)->where('name', $name)->exists();
        } catch (\Exception $ex) {
            throw $ex;
        }
    }
}
