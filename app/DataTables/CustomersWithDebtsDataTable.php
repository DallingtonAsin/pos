<?php

namespace App\DataTables;

use Yajra\DataTables\Services\DataTable;
use App\Models\Customer;
use App\Helpers\Helper;
use App\Repositories\CreditSaleRepository;

class CustomersWithDebtsDataTable extends DataTable
{

    protected $helper, $creditSaleRepository;

    public function __construct(Helper $helper, CreditSaleRepository $creditSaleRepository)
    {
        $this->helper = $helper;
        $this->creditSaleRepository = $creditSaleRepository;
    }
    /**
     * Build DataTable class.
     *
     * @param mixed $query Results from query() method.
     * @return \Yajra\DataTables\DataTableAbstract
     */
    public function dataTable($query)
    {

        return datatables($query)
            ->addIndexColumn()
            ->addColumn('action', function ($sale) {

                $btn = "";
                $btn .= '<a href="javascript:void(0);" id="view-sale" 
           data-toggle="tooltip" data-original-title="View"
            data-id="' . $sale->id . '" class="px-3 py-1 border border-secondary rounded text-secondary mx-2">
           <i class="fa fa-eye" ></i></a>';

                return $btn;
            })->addColumn('total_debt', function ($data) {
                return number_format($this->creditSaleRepository->getCustomerCreditSales($data->customer_id));
            })->addColumn('total_paid', function ($data) {
                return number_format(Helper::totalCustomerPayments($data->customer_id));
            })->addColumn('current_debt', function ($data) {
                return number_format($this->helper->getCustomerDebt($data->customer_id));
            })->rawColumns(['action']);
    }


    public function query()
    {

        return Customer::distinct()
            ->join('credit_sales', 'customers.id', '=', 'credit_sales.customer_id')
            ->select('customers.id as customer_id', 'customers.contact as customer_contact', 'customers.name as customer_name')
            ->get();
    }


    public function html()
    {
        return $this->builder()
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->addAction(['width' => '80px'])
            ->dom('Bfrtip')
            ->orderBy(1)->parameters($this->getBuilderParameters());
    }


    protected function getColumns()
    {
        return [ ];
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename()
    {
        return 'customers_with_debts' . date('YmdHis');
    }
}
