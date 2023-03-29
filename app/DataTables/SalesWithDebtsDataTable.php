<?php

namespace App\DataTables;

use Yajra\DataTables\Services\DataTable;
use Illuminate\Support\Facades\Gate;
use App\Helpers\Helper;
use App\Models\CreditSale;
use App\Repositories\CreditSaleRepository;

class SalesWithDebtsDataTable extends DataTable
{

    protected $creditSaleRepository;

    public function __construct(CreditSaleRepository $creditSaleRepository)
    {
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

                $btn = '<a href="javascript:void(0);" id="view-sale"
            data-toggle="tooltip" data-original-title="View"
             data-id="' . $sale->id . '" class="px-3 py-1 border border-secondary rounded text-secondary mx-2 pr-4">
            <i class="fa fa-eye" ></i></a>';

                if (Gate::allows('isAdmin')) {

                    $btn .= '<a href="javascript:void(0)" data-toggle="tooltip"
            data-id="' . $sale->id . '" data-item="' . $sale->item . '" data-original-title="Edit" id="edit-sale"
            class="px-3 py-1 border border-success rounded mx-2 edit-sale pr-3">
             <span class="fa fa-pen text-success"></span></a>';

                    $btn .= '<a href="javascript:void(0);" id="delete-sale"
            data-toggle="tooltip" data-original-title="Delete"
             data-id="' . $sale->id . '" class="px-3 py-1 border border-danger rounded mx-2 pl-2">
            <span class="fa fa-trash-alt text-danger" ></span></a>';
                }

                return $btn;
            })->addColumn('checkbox', function ($sale) {
                $checkBox = '<input type="checkbox" id="' . $sale->id . '"/>';
                return $checkBox;
            })->addColumn('cashier', function ($sale) {
                $cashier = Helper::getUser($sale->cashier_id);
                return $cashier->first_name . ' ' . $cashier->last_name;
            })->addColumn('customer', function ($sale) {
                $customer_name = null;
                if ($sale->customer_id) {
                    $customer = Helper::getCustomer($sale->customer_id);
                    $customer_name = $customer->name;
                }
                return $customer_name;
            })->editColumn('quantity', function ($data) {
                return Helper::convertNumber($data->quantity);
            })->editColumn('selling_price', function ($data) {
                return Helper::convertNumber($data->selling_price);
            })->editColumn('amount', function ($data) {
                return Helper::convertNumber($data->amount);
            })->editColumn('paid_amount', function ($data) {
                return Helper::convertNumber($data->paid_amount);
            })->editColumn('balance', function ($data) {
                return Helper::convertNumber($data->balance);
            })->editColumn('discount', function ($data) {
                return Helper::convertNumber($data->discount);
            })->rawColumns(['action', 'checkbox']);
    }


    public function query(CreditSale $model)
    {
        return $this->creditSaleRepository->get();
    }

    /**
     * Optional method if you want to use html builder.
     *
     * @return \Yajra\DataTables\Html\Builder
     */
    public function html()
    {
        return $this->builder()
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->addAction(['width' => '80px'])
            ->dom('Bfrtip')
            ->parameters($this->getBuilderParameters());
    }

    /**
     * Get columns.
     *
     * @return array
     */
    protected function getColumns()
    {
        return [
            'id',
            'item_id',
            'item',
            'quantity',
            'selling_price',
            'discount',
            'amount',
            'date',
            'customer_id',
            'cashier_id'
        ];
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename()
    {
        return 'Sales_with_debts' . date('YmdHis');
    }
}
