<?php

namespace App\Repositories;

use App\Models\SupplierDebt;

class SupplierDebtRepository
{
    protected $supplierDebt;

    public function __construct(SupplierDebt $supplierDebt)
    {
        $this->supplierDebt = $supplierDebt;
    }

    public function create($supplierDebtData)
    {
        try {
            return $this->supplierDebt->create($supplierDebtData);
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    public function find($id)
    {
        try {
            return $this->supplierDebt->find($id);
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    public function get()
    {
        try {
            return $this->supplierDebt->select(['id', 'supplier_id', 'amount', 'date', 'is_deleted', 'added_by'])->get();
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    public function update($id, $supplierDebtData)
    {
        try {
            return $this->supplierDebt->where('id', $id)->update($supplierDebtData);
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    public function delete($id)
    {
        try {
            $supplierDebt = $this->supplierDebt->find($id);
            $supplierDebt->delete();
            return $supplierDebt;
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    public function count()
    {
        try {
            return $this->supplierDebt->where('is_deleted', false)->count();
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    public function total()
    {
        try {
            return $this->supplierDebt->where('is_deleted', false)->sum('amount');
        } catch (\Exception $ex) {
            throw $ex;
        }
    }
    
    public function getSupplierTotalDebt($supplier_id)
    {
        try {
            return $this->supplierDebt->where('supplier_id', $supplier_id)->where('is_deleted', false)->sum('amount');
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    public function exists($supplier_id, $amount, $date)
    {
        try {
            return $this->supplierDebt
                ->where('supplier_id', $supplier_id)
                ->where('amount', $amount)
                ->where('date', $date)
                ->exists();
        } catch (\Exception $ex) {
            throw $ex;
        }
    }
}
