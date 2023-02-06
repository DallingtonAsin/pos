<?php

namespace App\DataTables;

use Yajra\DataTables\Services\DataTable;
use App\Models\Logs;

class LogsDataTable extends DataTable
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
        ->addColumn('action', function ($log) {

            // $btn = '<a href="javascript:void(0);" id="delete-log" 
            // data-toggle="tooltip" data-original-title="Delete" 
            // data-id="'.$log->id.'" class="px-3 py-1 border border-danger rounded mx-2 pl-3"">
            // <span class="glyphicon glyphicon-trash pr-4" ></span></a>';

           $btn = '<a href="javascript:void(0);" id="view-log" 
           data-toggle="tooltip" data-original-title="View" 
           data-id="'.$log->id.'" class="px-3 py-1 border border-secondary rounded text-secondary mx-2 pl-3">
           <i class="fa fa-eye" ></i></a>';

           return $btn;

        })->editColumn('logged_action', function ($data) {
            $user_action = $data->logged_action;
            return (strlen($user_action) > 25) ? substr($user_action, 0, 20).'...': $user_action;
        })->editColumn('date', function ($data) {
            return date('d/m/Y H:i', strtotime($data->date));
        })->orderColumn('id', function ($query, $order) {
            $order = 'desc';
            $query->orderBy('date', $order);
        })->rawColumns(['action', 'checkbox']);

    }

    
    public function query(Logs $model)
    {
        //return Logs::query()->orderBy('date', 'desc');
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
        return [
            'id',
            'name',
            'role',
            'logged_action',
            'ip_address',
            'date',
        ];
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename()
    {
        return 'Logs_' . date('YmdHis');
    }
}
