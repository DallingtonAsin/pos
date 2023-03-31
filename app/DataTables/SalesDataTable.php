<?php

namespace App\DataTables;

use Yajra\DataTables\Services\DataTable;
use App\Models\Sale;
use Illuminate\Support\Facades\Gate;
use App\Helpers\Helper;

class SalesDataTable extends DataTable
{
    /**
     * Build DataTable class.
     *
     * @param mixed $query Results from query() method.
     * @return \Yajra\DataTables\DataTableAbstract
     */
    public function dataTable($query)
    {

        return datatables($query)
            ->order(function ($query) {
                $query->orderBy('id', 'desc');
            })->addIndexColumn()
            ->addColumn('action', function ($sale) {

                $btn = "";

                if (Gate::allows('isAdmin')) {

                    $btn .= '<a href="javascript:void(0)" data-toggle="tooltip"
            data-id="' . $sale->id . '" data-item="' . $sale->item . '" data-original-title="Edit" id="edit-sale"
            class="px-3 py-1 border border-success rounded mx-2 edit-sale pr-3">
             <span class="fa fa-pen text-success"></span></a>';

                    $btn .= '<a href="javascript:void(0);" id="delete-sale"
            data-toggle="tooltip" data-original-title="Delete"
             data-id="' . $sale->id . '" class="px-3 py-1 border border-danger rounded mx-2">
            <span class="fa fa-trash-alt text-danger" ></span></a>';
                }

                $btn .= '<a href="javascript:void(0);" id="view-sale"
            data-toggle="tooltip" data-original-title="View"
             data-id="' . $sale->id . '" class="px-3 py-1 border border-secondary rounded text-secondary">
            <i class="fa fa-eye" ></i></a>';

                return $btn;
            })->addColumn('checkbox', function ($sale) {
                $checkBox = '<input type="checkbox" id="' . $sale->id . '"/>';
                return $checkBox;
            })->addColumn('cashier', function ($sale) {
                $cashier = Helper::getUser($sale->cashier_id);
                return ucfirst($cashier->first_name) . ' ' . ucfirst($cashier->last_name);
            })->editColumn('date', function ($sale) {
                $date_of_sale = $sale->date . ' '.$sale->time;
                return date('Y-m-d H:i A', strtotime($date_of_sale));
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
            })->editColumn('total_cost', function ($data) {
                return Helper::convertNumber($data->total_cost);
            })->editColumn('discount', function ($data) {
                return Helper::convertNumber($data->discount);
            })->editColumn('amount', function ($data) {
                return Helper::convertNumber($data->amount);
            })->rawColumns(['action', 'checkbox']);
    }


    public function query(Sale $model)
    {
        return $model->newQuery()->select('*');
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
            'total_cost',
            'discount',
            'amount',
            'date',
            'customer_id',
            'cashier_id',
            'created_at'
        ];
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename()
    {
        return 'Sales_' . date('YmdHis');
    }
}
