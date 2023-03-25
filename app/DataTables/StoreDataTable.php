<?php

namespace App\DataTables;

use App\Models\Store;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Services\DataTable;
use App\Helpers\Helper;

class StoreDataTable extends DataTable
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
            ->addColumn('action', function ($store) {

                $btn = '<a href="javascript:void(0)" data-toggle="tooltip" 
                data-id="'.$store->id.'" data-original-title="Edit" id="edit-store"
                  class="px-3 py-1 border border-success rounded mx-2 edit-store pr-4">
                 <span class="fa fa-pen text-success"></span></a>';
              
                $btn .= '<a href="javascript:void(0);" id="delete-store" 
                data-toggle="tooltip" data-original-title="Delete"
                 data-id="'.$store->id.'" class="px-3 py-1 border border-danger rounded mx-2 pr-4"">
                <span class="fa fa-trash-alt text-danger" ></span></a>';
    
               $btn .= '<a href="javascript:void(0);" id="view-store" 
               data-toggle="tooltip" data-original-title="View"
                data-id="'.$store->id.'" class="px-3 py-1 border border-secondary rounded text-secondary mx-2">
               <i class="fa fa-eye" ></i></a>';
    
               return $btn;

            })->addColumn('checkbox', function ($store) {
                $checkBox = '<input type="checkbox" id="' . $store->id . '"/>';
                return $checkBox;
            })->editColumn('is_deleted', function ($data) {
                return $data->is_deleted ? '<span class="text-danger">Yes</span>' : '<span class="text-dark">No</span>';
            })->editColumn('added_by', function ($store) {
                $user = Helper::getUser($store->added_by);
                return $user->first_name . ' ' . $user->last_name;
            })->rawColumns(['checkbox', 'is_deleted', 'action']);
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Models\Store $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(Store $model)
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
            ->setTableId('stores_datatable_table')
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
            'name',
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
        return 'Stores_' . date('YmdHis');
    }
}
