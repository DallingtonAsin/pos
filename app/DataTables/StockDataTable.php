<?php

namespace App\DataTables;

use Yajra\DataTables\Services\DataTable;
use Illuminate\Support\Facades\Gate;
use App\Models\Stock;

class StockDataTable extends DataTable
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
        // ->order(function($query){
        //        $query->orderBy('date_of_entry', 'desc');
        // })
        ->addIndexColumn()
        ->addColumn('action', function ($stock) {

           $btn = "";
            if(Gate::allows('isAdmin')){

            $btn .= '<a href="javascript:void(0)" data-toggle="tooltip"
            data-id="'.$stock->id.'" data-original-title="Edit" id="edit-stock"
            class="px-3 py-1 border border-success rounded mx-2 edit-stock pr-3">
             <span class="fa fa-pen text-success"></span></a>';

            $btn .= '<a href="javascript:void(0);" id="delete-stock"
            data-toggle="tooltip" data-original-title="Delete" data-id="'.$stock->id.'" 
            class="px-3 py-1 border border-danger rounded mx-2 pr-3"">
            <span class="fa fa-trash-alt text-danger" ></span></a>';


            }

             $btn .= '<a href="javascript:void(0);" id="view-stock"
            data-toggle="tooltip" data-original-title="View"
             data-id="'.$stock->id.'" class="px-3 py-1 border border-secondary rounded text-secondary mx-2">
            <i class="fa fa-eye" ></i></a>';


           return $btn;

        })->addColumn('checkbox', function ($stock) {
              $checkBox = '<input type="checkbox" id="'.$stock->id.'"/>';
             return $checkBox;
        })->editColumn('quantity', function ($data) {
            return number_format($data->quantity);
        })->editColumn('threshold_qty', function ($data) {
            return number_format($data->threshold_qty);
        })->editColumn('buying_price', function ($data) {
            return number_format($data->buying_price);
        })->editColumn('selling_price', function ($data) {
            return number_format($data->selling_price);
        })->editColumn('wholesale_price', function ($data) {
            return number_format($data->wholesale_price);
        })->rawColumns(['action', 'checkbox']);

    }


    public function query(Stock $model)
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
        ->orderBy(1)->parameters($this->getBuilderParameters());
    }

    /**
     * Get columns.
     *
     * @return array
     */
    protected function getColumns()
    {

      //  if(Gate::allows('isAdmin')){

        return [
            'id',
            'item_code',
            'item',
            'quantity',
            'threshold_qty',
            'buying_price',
            'selling_price',
            'wholesale_price',
            'supplier'
        ];



    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename()
    {
        return 'Stock_' . date('YmdHis');
    }
}
