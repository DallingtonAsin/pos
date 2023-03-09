<?php

namespace App\DataTables;

use Yajra\DataTables\Services\DataTable;
use App\Models\Purchase;
use App\Models\Supplier;
use App\Models\Stock;

class PurchasesDataTable extends DataTable
{
    /**
     * Build DataTable class.
     *
     * @param mixed $query Results from query() method.
     * @return \Yajra\DataTables\DataTableAbstract
     */
    public function dataTable($query)
    {
        return datatables($query)->order(function($query){
               $query->orderBy('id', 'desc');
        })->addIndexColumn()
        ->addColumn('action', function ($purchase) {
            $btn = '<a href="javascript:void(0)" data-toggle="tooltip"
            data-id="'.$purchase->id.'" data-original-title="Edit" id="edit-purchase"
              class="px-3 py-1 border border-success rounded mx-2 edit-purchase pr-4">
             <span class="fa fa-pen text-success"></span></a>';

            // $btn .= '<a href="javascript:void(0);" id="delete-purchase"
            // data-toggle="tooltip" data-original-title="Delete" data-id="'.$purchase->id.'"
            //  class="px-3 py-1 border border-danger rounded mx-2 pr-4"">
            // <span class="fa fa-trash-alt text-danger" ></span></a>';

           $btn .= '<a href="javascript:void(0);" id="view-purchase"
           data-toggle="tooltip" data-original-title="View" data-id="'.$purchase->id.'" 
           class="px-3 py-1 border border-secondary rounded text-secondary mx-2">
           <i class="fa fa-eye" ></i></a>';

           return $btn;

        })->addColumn('checkbox', function ($purchase) {
              $checkBox = '<input type="checkbox" id="'.$purchase->id.'"/>';
             return $checkBox;
        })->addColumn('item', function ($data) {
            $item = Stock::find($data->item_id);
            return $item->item;
        })->addColumn('item_code', function ($data) {
            $item = Stock::find($data->item_id);
            return $item->item_code;
        })->addColumn('supplier', function ($data) {
            $supplier = Supplier::find($data->supplier_id);
            return $supplier->name;
        })->editColumn('quantity', function ($data) {
            return number_format($data->quantity);
        })->editColumn('cost_price_per_item', function ($data) {
            return number_format($data->cost_price_per_item);
        })->editColumn('total_cost_price', function ($data) {
            return number_format($data->total_cost_price);
        })->editColumn('date', function ($data) {
            return date('d-m-Y H:i', strtotime($data->date));
        })->rawColumns(['action', 'checkbox']);
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\User $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(Purchase $model)
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
            'item_code',
            'item',
            'quantity',
            'cost_price_per_item',
            'total_cost_price',
            'supplier',
            'recorded_by',
            'date'
        ];
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename()
    {
        return 'Purchases_' . date('YmdHis');
    }
}
