<?php

namespace App\DataTables;

use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Services\DataTable;
use App\Helpers\Helper;
use App\Models\Supplier;
use App\Models\SupplierDebt;


class SupplierDebtDataTable extends DataTable
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
            ->addColumn('action', function ($supplierDebt) {

                $btn = '<a href="javascript:void(0)" data-toggle="tooltip" 
                data-id="' . $supplierDebt->id . '" data-original-title="Edit" id="edit-supplier-debt"
                  class="px-3 py-1 border border-success rounded mx-2 edit-supplier-debt pr-4">
                 <span class="fa fa-pen text-success"></span></a>';

                $btn .= '<a href="javascript:void(0);" id="delete-supplier-debt" 
                data-toggle="tooltip" data-original-title="Delete"
                 data-id="' . $supplierDebt->id . '" class="px-3 py-1 border border-danger rounded mx-2 pr-4"">
                <span class="fa fa-trash-alt text-danger" ></span></a>';

                $btn .= '<a href="javascript:void(0);" id="view-supplier-debt" 
               data-toggle="tooltip" data-original-title="View"
                data-id="' . $supplierDebt->id . '" class="px-3 py-1 border border-secondary rounded text-secondary mx-2">
               <i class="fa fa-eye" ></i></a>';

                return $btn;
            })->addColumn('supplier', function ($data) {
                $supplier = Supplier::find($data->supplier_id);
                return $supplier->name;
            })->editColumn('is_deleted', function ($data) {
                return $data->is_deleted ? '<span class="text-danger">Yes</span>' : '<span class="text-dark">No</span>';
            })->editColumn('added_by', function ($supplierDebt) {
                $user = Helper::getUser($supplierDebt->added_by);
                return $user->first_name . ' ' . $user->last_name;
            })->editColumn('amount', function ($supplierDebt) {
                return number_format($supplierDebt->amount);
            })->addColumn('checkbox', function ($supplierDebt) {
                $checkBox = '<input type="checkbox" id="' . $supplierDebt->id . '"/>';
                return $checkBox;
            })->rawColumns(['checkbox', 'is_deleted', 'action']);
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Models\Store $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(SupplierDebt $model)
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
            ->setTableId('supplier_credits_datatable_table')
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
            'supplier_id',
            'amount',
            'date',
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
        return 'SupplierDebts_' . date('YmdHis');
    }
}
