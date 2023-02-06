<?php

namespace App\DataTables;

use Yajra\DataTables\Services\DataTable;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\Expense;

class ExpensesDataTable extends DataTable
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
        ->order(function($query){
               $query->orderBy('date_of_expenditure', 'desc');
        })->addIndexColumn()
        ->addColumn('action', function ($expense) {
            $btn = '<a href="javascript:void(0)" data-toggle="tooltip" 
            data-id="'.$expense->id.'" data-original-title="Edit" id="edit-expense"
              class="px-3 py-1 border border-success rounded mx-2 edit-expense pr-4">
             <span class="fa fa-pen text-success"></span></a>';
          
            $btn .= '<a href="javascript:void(0);" id="delete-expense" 
            data-toggle="tooltip" data-original-title="Delete" data-id="'.$expense->id.'"
             class="px-3 py-1 border border-danger rounded mx-2 pr-4"">
            <span class="fa fa-trash-alt text-danger" ></span></a>';

           $btn .= '<a href="javascript:void(0);" id="view-expense" 
           data-toggle="tooltip" data-original-title="View" data-id="'.$expense->id.'" 
           class="px-3 py-1 border border-secondary rounded text-secondary mx-2">
           <i class="fa fa-eye" ></i></a>';

           return $btn;

        })->addColumn('checkbox', function ($expense) {
              $checkBox = '<input type="checkbox" id="'.$expense->id.'"/>';
             return $checkBox;
        })->editColumn('amount', function ($data) {
            return number_format($data->amount);
        })->rawColumns(['action', 'checkbox']);
    }

 
    public function query(Expense $model)
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
            'expense_type',
            'amount',
            'date_of_expenditure'
        ];
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename()
    {
        return 'Expenses_' . date('YmdHis');
    }
}
