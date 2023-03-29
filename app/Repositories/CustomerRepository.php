<?php

namespace App\Repositories;

use App\Models\Customer;
use App\Repositories\CreditSaleRepository;
use App\Repositories\CustomerDebtPaymentRepository;

class CustomerRepository
{
    protected $customer, $creditSaleRepository, $customerDebtPaymentRepository;

    public function __construct(Customer $customer, CreditSaleRepository $creditSaleRepository, CustomerDebtPaymentRepository $customerDebtPaymentRepository)
    {
        $this->customer = $customer;
        $this->creditSaleRepository = $creditSaleRepository;
        $this->customerDebtPaymentRepository = $customerDebtPaymentRepository;
    }

    public function create($customerData)
    {
        try {
            return $this->customer->create($customerData);
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    public function find($id)
    {
        try {
            return $this->customer->find($id);
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    public function get()
    {
        try {
            return $this->customer->get();
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    public function update($id, $customerData)
    {
        try {
            return $this->customer->where('id', $id)->update($customerData);
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    public function delete($id)
    {
        try {
            $customer = $this->customer->find($id);
            $customer->delete();
            return $customer;
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    public function count()
    {
        try {
            return $this->customer->count();
        } catch (\Exception $ex) {
            throw $ex;
        }
    }


    public function exists($name, $contact)
    {
        try {
            return $this->customer->where('name', $name)->where('contact', $contact)->exists();
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    public function existsonUpdate($id, $name, $contact)
    {
        try {
            return $this->customer->where('id', '!=', $id)->where('name', $name)->where('contact', $contact)->exists();
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    public function getCustomerOutstandingDebt($customer_id){
        $credit_sales = $this->creditSaleRepository->getCustomerCreditSales($customer_id);
        $total_debt_paid = $this->customerDebtPaymentRepository->getCustomerPaidDebtAmount($customer_id);
        return $credit_sales - $total_debt_paid;
    }
}
