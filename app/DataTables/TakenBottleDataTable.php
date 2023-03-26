<?php

namespace App\DataTables;

use App\Helpers\Helper;
use App\Models\Customer;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Services\DataTable;
use App\Models\TakenBottle;
use App\Models\Stock;

class TakenBottleDataTable extends DataTable
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
                $query->orderBy('created_at', 'desc');
            })->addIndexColumn()
            ->addColumn('action', function ($takenBottle) {

                $btn = '<a href="javascript:void(0)" data-toggle="tooltip" 
                data-id="' . $takenBottle->id . '" data-original-title="Edit" id="edit-taken-bottle"
                  class="px-3 py-1 border border-success rounded mx-2 edit-taken-bottle pr-4">
                 <span class="fa fa-pen text-success"></span></a>';

                $btn .= '<a href="javascript:void(0);" id="delete-taken-bottle" 
                data-toggle="tooltip" data-original-title="Delete"
                 data-id="' . $takenBottle->id . '" class="px-3 py-1 border border-danger rounded mx-2 pr-4"">
                <span class="fa fa-trash-alt text-danger" ></span></a>';

                $btn .= '<a href="javascript:void(0);" id="view-taken-bottle" 
               data-toggle="tooltip" data-original-title="View"
                data-id="' . $takenBottle->id . '" class="px-3 py-1 border border-secondary rounded text-secondary mx-2">
               <i class="fa fa-eye" ></i></a>';

                return $btn;
            })->addColumn('customer', function ($data) {
                $customer = Customer::find($data->customer_id);
                return $customer->name;
            })->addColumn('bottle', function ($data) {
                $stock = Stock::find($data->bottle_id);
                return $stock->item;
            })->editColumn('is_deleted', function ($data) {
                return $data->is_deleted ? '<span class="text-danger">Yes</span>' : '<span class="text-dark">No</span>';
            })->editColumn('added_by', function ($takenBottle) {
                $user = Helper::getUser($takenBottle->added_by);
                return $user->first_name . ' ' . $user->last_name;
            })->addColumn('checkbox', function ($takenBottle) {
                $checkBox = '<input type="checkbox" id="' . $takenBottle->id . '"/>';
                return $checkBox;
            })->rawColumns(['checkbox', 'is_deleted', 'action']);
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Models\TakenBottle $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(TakenBottle $model)
    {
        return $model->newQuery();
    }

    /**
     * Optional method if you want to use html builder.
     *
     * @return \Yajra\DataTables\Html\Builder
     */
    public function html()
    {
        return $this->builder()
            ->setTableId('taken_bottles_datatable_table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->dom('Bfrtip')
            ->orderBy(1)
            ->buttons(
                Button::make('create'),
                Button::make('export'),
                Button::make('print'),
                Button::make('reset'),
                Button::make('reload')
            );
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
            'customer_id',
            'bottle_id',
            'quantity',
            'taken_on',
            'is_returned',
            'returned_on',
            'is_deleted',
            'added_by'
        ];
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename()
    {
        return 'TakenBottles_' . date('YmdHis');
    }
}