<?php

namespace App\Repositories;

use App\Models\CreditSale;
use App\Repositories\CustomerDebtPaymentRepository;

class CreditSaleRepository
{
    protected $creditSale, $customerDebtPaymentRepository;

    public function __construct(CreditSale $creditSale, CustomerDebtPaymentRepository $customerDebtPaymentRepository)
    {
        $this->creditSale = $creditSale;
        $this->customerDebtPaymentRepository = $customerDebtPaymentRepository;
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

            return $this->creditSale->join('sales', 'credit_sales.sale_order_number', '=', 'sales.order_number')
                ->select(
                    'sales.order_number',
                    'sales.item_code',
                    'sales.item',
                    'sales.quantity',
                    'sales.original_price',
                    'sales.selling_price',
                    'sales.total_buying_cost',
                    'sales.total_cost',
                    'sales.cashier_id',
                    'sales.discount',
                    'sales.date',
                    'sales.time',
                    'credit_sales.customer_id',
                    'credit_sales.amount_paid',
                    'credit_sales.amount_due'
                )
                ->orderBy('sales.id', 'desc')
                ->get();
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
            return $this->creditSale->join('sales', 'credit_sales.sale_order_number', '=', 'sales.order_number')
                ->select(
                    'sales.order_number',
                    'sales.item_code',
                    'sales.item',
                    'sales.quantity',
                    'sales.original_price',
                    'sales.selling_price',
                    'sales.total_buying_cost',
                    'sales.total_cost',
                    'sales.cashier_id',
                    'sales.discount',
                    'sales.date',
                    'sales.time',
                    'credit_sales.customer_id',
                    'credit_sales.amount_paid',
                    'credit_sales.amount_due'
                )
                ->orderBy('sales.id', 'desc')
                ->count();
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    public function totalCreditSales($year = null, $month = null, $date = null)
    {
        $data = $this->creditSale;
        if (!empty($year)) {
            $data = $data->whereYear('date', $year);
        }
        if (!empty($month)) {
            $data = $data->whereMonth('date', $month);
        }
        if (!empty($date)) {
            $data = $data->whereDate('date', $date);
        }
        return $data->sum('amount_due');
    }

    public function outstandingCreditFromSales($year = null, $month = null, $date = null)
    {
        try {

            $total_customer_debt_payments = $this->customerDebtPaymentRepository->totalPaid($year, $month, $date);
            $total_credit_sales = $this->totalCreditSales($year, $month, $date);
            $outstanding_credit = $total_credit_sales - $total_customer_debt_payments;
            return $outstanding_credit;
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    public function getCustomerCreditSales($customer_id){
        return $this->creditSale->where('customer_id', $customer_id)->sum('amount_due');
    }
}
