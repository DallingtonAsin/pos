<?php

namespace App\DataTables;

use Yajra\DataTables\Services\DataTable;
use App\Models\Sale;
use App\Helpers\Helper;

class CustomersWithDebtsDataTable extends DataTable
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
               $query->orderBy('date', 'desc');
        })->addIndexColumn()
        ->addColumn('action', function ($sale) {
            
            $btn = '<a href="javascript:void(0)" data-toggle="tooltip" 
            data-id="'.$sale->id.'" data-original-title="Edit" id="edit-sale"
              class="px-3 py-1 border border-success rounded mx-2 edit-sale">
             <span class="fa fa-pen pr-4"></span></a>';
            //   if(Gate::allows('isAdmin')){
            // $btn .= '<a href="javascript:void(0);" id="delete-sale" 
            // data-toggle="tooltip" data-original-title="Delete"
            //  data-id="'.$sale->id.'" class="px-3 py-1 border border-danger rounded mx-2 pr-4"">
            // <span class="fa fa-trash-alt text-danger" ></span></a>';
            //   }
           $btn .= '<a href="javascript:void(0);" id="view-sale" 
           data-toggle="tooltip" data-original-title="View"
            data-id="'.$sale->id.'" class="px-3 py-1 border border-secondary rounded text-secondary mx-2">
           <i class="fa fa-eye" ></i></a>';

           return $btn;

        })->addColumn('checkbox', function ($sale) {
              $checkBox = '<input type="checkbox" id="'.$sale->id.'"/>';
             return $checkBox;
        })->addColumn('cashier', function ($sale) {
            $cashier = Helper::getUser($sale->cashier_id);
            return $cashier->first_name . ' ' . $cashier->last_name;
        })->addColumn('customer', function ($sale) {
            $customer_name = null;
            if ($sale->customer_id) {
                $customer = Helper::getCustomer($sale->customer_id);
                $customer_name = $customer->name;
            }
            return $customer_name;
        })->editColumn('amount', function ($data) {
            return number_format($data->amount);
        })->editColumn('paid_amount', function ($data) {
            return number_format($data->paid_amount);
        })->editColumn('balance', function ($data) {
            $bal = number_format($data->balance);
            return '<span class="text-danger" >'.$bal.'</span>';
        })->rawColumns(['action', 'checkbox', 'balance']);


    }

  
    public function query(Sale $model)
    {
        
        return $model->newQuery()->select('*')->where('balance', '>', 0)->where('fully_paid', 0);
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
            'item_id',
            'item',
            'quantity',
            'selling_price',
            'discount',
            'amount',
            'date',
            'customer_id',
            'cashier_id'
        ];
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename()
    {
        return 'customers_with_debts' . date('YmdHis');
    }
}

