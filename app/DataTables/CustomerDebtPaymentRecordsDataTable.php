<?php

namespace App\DataTables;

use Yajra\DataTables\Services\DataTable;
use Illuminate\Support\Facades\Gate;
use App\Models\CustomerDebtPayment;
use App\Helpers\Helper;

class CustomerDebtPaymentRecordsDataTable extends DataTable
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
        ->addColumn('action', function ($payment) {
            
            $btn = '<a href="javascript:void(0)" data-toggle="tooltip" 
            data-id="'.$payment->id.'" data-original-title="Edit" id="edit-payment"
              class="px-3 py-1 border border-success rounded mx-2 edit-payment">
             <span class="fa fa-pen text-success"></span></a>';
              if(Gate::allows('isAdmin')){
            $btn .= '<a href="javascript:void(0);" id="delete-payment" 
            data-toggle="tooltip" data-original-title="Delete"
             data-id="'.$payment->id.'" class="px-3 py-1 border border-danger rounded mx-2 pr-4"">
            <span class="fa fa-trash-alt text-danger" ></span></a>';
              }
           $btn .= '<a href="javascript:void(0);" id="view-payment" 
           data-toggle="tooltip" data-original-title="View"
            data-id="'.$payment->id.'" class="px-3 py-1 border border-secondary rounded text-secondary mx-2">
           <i class="fa fa-eye" ></i></a>';

           return $btn;

        })->addColumn('checkbox', function ($payment) {
              $checkBox = '<input type="checkbox" id="'.$payment->id.'"/>';
             return $checkBox;
        })->editColumn('paid_amount', function ($data) {
            return number_format($data->paid_amount);
        })->editColumn('balance', function ($data) {
            return number_format($data->balance);
        })->editColumn('recorded_by', function ($data) {
            $user = Helper::getUser($data->recorded_by);
            return ucfirst($user->first_name). ' '.ucfirst($user->last_name);
        })->addColumn('customer', function ($data) {
            $customer = Helper::getCustomer($data->customer_id);
            return $customer->name;
        })->rawColumns(['action', 'checkbox']);


    }

  
    public function query(CustomerDebtPayment $model)
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
            'customer_id',
            'paid_amount',
            'balance',
            'date',
            'recorded_by',
        ];
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename()
    {
        return 'CustomerDebtPaymentRecords_' . date('YmdHis');
    }
}

