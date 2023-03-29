<?php

namespace App\Repositories;

use App\Models\CustomerDebtPayment;

class CustomerDebtPaymentRepository
{
    protected $customerDebtPayment;

    public function __construct(CustomerDebtPayment $customerDebtPayment)
    {
        $this->customerDebtPayment = $customerDebtPayment;
    }

    public function create($customerDebtPaymentData)
    {
        try {
            return $this->customerDebtPayment->create($customerDebtPaymentData);
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    public function find($id)
    {
        try {
            return $this->customerDebtPayment->find($id);
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    public function get()
    {
        try {
            return $this->customerDebtPayment->select(['id', 'name', 'is_deleted', 'added_by'])->get();
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    public function update($id, $customerDebtPaymentData)
    {
        try {
            return $this->customerDebtPayment->where('id', $id)->update($customerDebtPaymentData);
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    public function delete($id)
    {
        try {
            $customerDebtPayment = $this->customerDebtPayment->find($id);
            $customerDebtPayment->delete();
            return $customerDebtPayment;
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    public function count()
    {
        try {
            return $this->customerDebtPayment->count();
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    public function totalPaid($year = null, $month = null, $date = null)
    {
        try {

            $data = $this->customerDebtPayment;
            if (!empty($year)) {
                $data = $data->whereYear('date', $year);
            }
            if (!empty($month)) {
                $data = $data->whereMonth('date', $month);
            }
            if (!empty($date)) {
                $data = $data->whereDate('date', $date);
            }
            return $data->sum('paid_amount');
        } catch (\Exception $ex) {
            throw $ex;
        }
    }
}
