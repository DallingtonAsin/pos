<?php

namespace App\DataTables;

use Yajra\DataTables\Services\DataTable;
use Illuminate\Support\Facades\Gate;
use App\Models\Damage;
use App\Models\Stock;


class DamagesDataTable extends DataTable
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
                $query->orderBy('recorded_on', 'desc');
            })->addIndexColumn()
            ->addColumn('action', function ($damage) {
                $btn = "";
                if (Gate::allows('isAdmin')) {

                    $btn .= '<a href="javascript:void(0)" data-toggle="tooltip"
            data-id="' . $damage->id . '" data-original-title="Edit" id="edit-damage"
              class="px-3 py-1 border border-success rounded mx-2 edit-damage pr-4">
             <span class="fa fa-pen text-success"></span></a>';


                    $btn .= '<a href="javascript:void(0);" id="delete-damage"
            data-toggle="tooltip" data-original-title="Delete" data-id="' . $damage->id . '" 
            class="px-3 py-1 border border-danger rounded mx-2 pr-4">
            <span class="fa fa-trash-alt text-danger" ></span></a>';
                }

                $btn .= '<a href="javascript:void(0);" id="view-damage"
            data-toggle="tooltip" data-original-title="View" data-id="' . $damage->id . '"
             class="px-3 py-1 border border-secondary rounded text-secondary mx-2">
            <i class="fa fa-eye" ></i></a>';



                return $btn;
            })->addColumn('checkbox', function ($damage) {
                $checkBox = '<input type="checkbox" id="' . $damage->id . '"/>';
                return $checkBox;
            })->addColumn('item', function ($data) {
                $item = Stock::find($data->item_id);
                return $item->item;
            })->addColumn('buying_price', function ($data) {
                $item = Stock::find($data->item_id);
                return number_format($item->buying_price);
            })->addColumn('lost_amount', function ($data) {
                $item = Stock::find($data->item_id);
                return number_format($data->quantity*$item->buying_price);
            })->editColumn('quantity', function ($data) {
                return number_format($data->quantity);
            })->editColumn('recorded_on', function ($data) {
                return date('d/m/Y H:i', strtotime($data->recorded_on));
            })->rawColumns(['action', 'checkbox']);
    }


    public function query(Damage $model)
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
        return [
            'id',
            'item_id',
            'item',
            'category',
            'quantity',
            'buying_price',
            'total_cost',
            'recorded_on',
        ];
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename()
    {
        return 'Damages_' . date('YmdHis');
    }
}
