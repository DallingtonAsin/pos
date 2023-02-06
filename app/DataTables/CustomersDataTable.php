<?php

namespace App\DataTables;

use Yajra\DataTables\Services\DataTable;
use Illuminate\Support\Facades\Gate;

use App\Models\Customer;
use App\Helpers\Helper;

class CustomersDataTable extends DataTable
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
               $query->orderBy('created_at', 'desc');
        })->addIndexColumn()
        ->addColumn('action', function ($customer) {
            
            $btn = '<a href="javascript:void(0)" data-toggle="tooltip" 
            data-id="'.$customer->id.'" data-original-title="Edit" id="edit-customer"
              class="px-3 py-1 border border-success rounded mx-2 edit-customer">
             <span class="fa fa-pen pr-4"></span></a>';
              if(Gate::allows('isAdmin')){
            $btn .= '<a href="javascript:void(0);" id="delete-customer" 
            data-toggle="tooltip" data-original-title="Delete"
             data-id="'.$customer->id.'" class="px-3 py-1 border border-danger rounded mx-2 pr-4"">
            <span class="fa fa-trash-alt text-danger" ></span></a>';
              }
           $btn .= '<a href="javascript:void(0);" id="view-customer" 
           data-toggle="tooltip" data-original-title="View"
            data-id="'.$customer->id.'" class="px-3 py-1 border border-secondary rounded text-secondary mx-2">
           <i class="fa fa-eye" ></i></a>';

           return $btn;

        })->addColumn('checkbox', function ($customer) {
              $checkBox = '<input type="checkbox" id="'.$customer->id.'"/>';
             return $checkBox;
        })->editColumn('credit', function ($data) {
            return number_format($data->credit);
        })->editColumn('debt', function ($data) {
            return number_format($data->debt);
        })->editColumn('item_taken', function ($data) {
            return Helper::GetItemName($data->item_taken);
        })->rawColumns(['action', 'checkbox']);


    }

  
    public function query(Customer $model)
    {
        
        return $model->newQuery()->select('*');
    }

    
    public function html()
    {
        return $this->builder()
        ->columns($this->getColumns())
        ->minifiedAjax()
        ->addAction(['width' => '80px'])
        ->dom('Bfrtip')
        ->orderBy(1)->parameters($this->getBuilderParameters());
    }

   
    protected function getColumns()
    {
        return [
            'id',
            'name',
            'item_taken',
            'contact',
            'credit',
            'debt',
            'taken_on',
        ];
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename()
    {
        return 'customers_' . date('YmdHis');
    }
}
