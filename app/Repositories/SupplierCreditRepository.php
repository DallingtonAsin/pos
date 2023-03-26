<?php

namespace App\Repositories;

use App\Models\SupplierCredit;

class SupplierCreditRepository
{
    protected $supplierCredit;

    public function __construct(SupplierCredit $supplierCredit)
    {
        $this->supplierCredit = $supplierCredit;
    }

    public function create($supplierCreditData)
    {
        try {
            return $this->supplierCredit->create($supplierCreditData);
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    public function find($id)
    {
        try {
            return $this->supplierCredit->find($id);
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    public function get()
    {
        try {
            return $this->supplierCredit->select(['id', 'supplier_id', 'amount', 'date', 'is_deleted', 'added_by'])->get();
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    public function update($id, $supplierCreditData)
    {
        try {
            return $this->supplierCredit->where('id', $id)->update($supplierCreditData);
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    public function delete($id)
    {
        try {
            $supplierCredit = $this->supplierCredit->find($id);
            $supplierCredit->delete();
            return $supplierCredit;
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    public function count()
    {
        try {
            return $this->supplierCredit->where('is_deleted', false)->count();
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    public function total()
    {
        try {
            return $this->supplierCredit->where('is_deleted', false)->sum('amount');
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    public function exists($supplier_id, $amount, $date)
    {
        try {
            return $this->supplierCredit
                ->where('supplier_id', $supplier_id)
                ->where('amount', $amount)
                ->where('date', $date)
                ->exists();
        } catch (\Exception $ex) {
            throw $ex;
        }
    }
}
