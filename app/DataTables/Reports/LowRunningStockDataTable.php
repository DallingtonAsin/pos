<?php

namespace App\DataTables\Reports;

use Yajra\DataTables\Services\DataTable;
use App\Models\Stock;
use App\Models\Supplier;


class LowRunningStockDataTable extends DataTable
{
    /**
     * Build DataTable class.
     *
     * @param mixed $query Results from query() method.
     * @return \Yajra\DataTables\DataTableAbstract
     */
    public function dataTable($query)
    {

        return datatables($query)->addIndexColumn()
            ->filter(function ($query) {
                $query->whereColumn('quantity', '<=', 'threshold_qty');
            })->addColumn('supplier', function ($data) {

                if ($data->suppler_id) {
                    $supplier = Supplier::find($data->supplier_id);
                    return $supplier->name;
                } else {
                    return null;
                }
            })->editColumn('quantity', function ($data) {
                return number_format($data->quantity);
            })->editColumn('threshold_qty', function ($data) {
                return number_format($data->threshold_qty);
            })->editColumn('buying_price', function ($data) {
                return number_format($data->buying_price);
            })->editColumn('selling_price', function ($data) {
                return number_format($data->selling_price);
            })->rawColumns(['action', 'checkbox']);
    }


    public function query(Stock $model)
    {

        return $model->newQuery()->select(
            'id',
            'item_code',
            'item',
            'quantity',
            'threshold_qty',
            'buying_price',
            'selling_price',
            'supplier_id'
        );
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
            'stock_id',
            'stock',
            'quantity',
            'threshold_qty',
            'buying_price',
            'selling_price',
            'supplier_id'
        ];
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename()
    {
        return 'LowRunningStock_' . date('YmdHis');
    }
}
