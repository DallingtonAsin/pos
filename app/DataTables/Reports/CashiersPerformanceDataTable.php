<?php

namespace App\DataTables\Reports;

use Yajra\DataTables\Services\DataTable;
use App\Models\TopCashier;
use App\Helpers\Helper;

class CashiersPerformanceDataTable extends DataTable
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
            ->addIndexColumn()->editColumn('totalsales', function ($data) {
                return number_format($data->totalsales);
            })->addColumn('cashier', function ($data) {
                $user = Helper::getUser($data->cashier_id);
                return ucfirst($user->first_name). ' '.ucfirst($user->last_name);
            })->addColumn('percent', function ($data) {
                $total  = TopCashier::sum('totalsales');
                return round(($data->totalsales / $total) * 100, 2);
            });
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Model\TopCashier $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(TopCashier $model)
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
        return [];
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename()
    {
        return 'Reports\CashiersPerformance_' . date('YmdHis');
    }
}
