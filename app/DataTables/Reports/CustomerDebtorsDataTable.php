<?php

namespace App\DataTables\Reports;

use App\User;
use Yajra\DataTables\Services\DataTable;
use App\Models\DebtorsCustomer;

class CustomerDebtorsDataTable extends DataTable
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
        ->addIndexColumn()->addColumn('volumeofsales', function ($data) {
             return number_format($data->volumeofsales);
         })->editColumn('debts', function ($data){
            return number_format($data->debts);
        })->addColumn('percent', function ($data){
             $total  = DebtorsCustomer::sum('debts');
             return round(($data->debts/$total)*100, 1);
         });
    }


    /**
     * Get query source of dataTable.
     *
     * @param \App\Model\DebtorsCustomer $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(DebtorsCustomer $model)
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
        return 'Reports\CustomerDebtors_' . date('YmdHis');
    }
}
