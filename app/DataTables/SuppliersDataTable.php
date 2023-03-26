<?php

namespace App\DataTables;

use Yajra\DataTables\Services\DataTable;
use App\Models\Supplier;
use App\Repositories\SupplierCreditRepository;
use App\Repositories\SupplierDebtRepository;

class SuppliersDataTable extends DataTable
{

    protected $supplierCreditRepository, $supplierDebtRepository;

    public function __construct(SupplierCreditRepository $supplierCreditRepository, SupplierDebtRepository $supplierDebtRepository)
    {
        $this->supplierCreditRepository = $supplierCreditRepository;
        $this->supplierDebtRepository = $supplierDebtRepository;
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
            ->order(function ($query) {
                $query->orderBy('created_at', 'desc');
            })->addIndexColumn()
            ->addColumn('action', function ($supplier) {

                $btn = '<a href="javascript:void(0)" data-toggle="tooltip" 
            data-id="' . $supplier->id . '" data-original-title="Edit" id="edit-supplier"
              class="px-3 py-1 border border-success rounded mx-2 edit-supplier pr-4">
             <span class="fa fa-pen text-success"></span></a>';

                $btn .= '<a href="javascript:void(0);" id="delete-supplier" 
            data-toggle="tooltip" data-original-title="Delete"
             data-id="' . $supplier->id . '" class="px-3 py-1 border border-danger rounded mx-2 pr-4"">
            <span class="fa fa-trash-alt text-danger" ></span></a>';

                $btn .= '<a href="javascript:void(0);" id="view-supplier" 
           data-toggle="tooltip" data-original-title="View"
            data-id="' . $supplier->id . '" class="px-3 py-1 border border-secondary rounded text-secondary mx-2">
           <i class="fa fa-eye" ></i></a>';

                return $btn;
            })->addColumn('checkbox', function ($supplier) {
                $checkBox = '<input type="checkbox" id="' . $supplier->id . '"/>';
                return $checkBox;
            })->addColumn('credit', function ($data) {
                $credit = $this->supplierCreditRepository->getSupplierTotalCredit($data->id);
                return number_format($credit);
            })->addColumn('debt', function ($data) {
                $debt = $this->supplierDebtRepository->getSupplierTotalDebt($data->id);
                return number_format($debt);
            })->rawColumns(['action', 'checkbox']);
    }

    public function query(Supplier $model)
    {
        return $model->newQuery()->select(
            'id',
            'name',
            'contact',
            'address',
            'email'
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
            'name',
            'contact',
            'address',
            'email',
            'credit',
            'debt',
        ];
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename()
    {
        return 'Suppliers_' . date('YmdHis');
    }
}
