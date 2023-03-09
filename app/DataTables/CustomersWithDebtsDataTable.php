<?php

namespace App\DataTables;

use Yajra\DataTables\Services\DataTable;
use App\Models\Sale;
use App\Models\Customer;
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
        ->addIndexColumn()
        ->addColumn('action', function ($sale) {
            
            $btn = "";
            // $btn = '<a href="javascript:void(0)" data-toggle="tooltip" 
            // data-id="'.$sale->id.'" data-original-title="Edit" id="edit-sale"
            //   class="px-3 py-1 border border-success rounded mx-2 edit-sale">
            //  <span class="fa fa-pen text-success"></span></a>';
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

        })->editColumn('debt', function ($data) {
            return number_format(Helper::customerDebt($data->customer_id));
        })->rawColumns(['action']);


    }

  
    public function query(Sale $model)
    {
        
       // return $model->newQuery()->select('*')->where('balance', '>', 0)->where('fully_paid', 0);

       return Customer::distinct()
            ->join('sales', 'customers.id', '=', 'sales.customer_id')
            ->select('customers.id as customer_id', 'customers.contact as customer_contact', 'customers.name as customer_name')
            ->get();
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

