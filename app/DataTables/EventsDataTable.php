<?php

namespace App\DataTables;

use Yajra\DataTables\Services\DataTable;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\Event;

class EventsDataTable extends DataTable
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
               $query->orderBy('start_date', 'desc');
        })->addIndexColumn()
         ->filter(function ($query){
           $query->where('start_date', '>=', date('Y-m-d'));
        })
        ->addColumn('action', function ($event) {

            $btn = '<a href="javascript:void(0)" data-toggle="tooltip" 
            data-id="'.$event->id.'" data-original-title="Edit" id="edit-event"
              class="px-3 py-1 border border-success rounded mx-2 edit-event pr-3">
             <span class="fa fa-pen text-success"></span></a>';

            $btn .= '<a href="javascript:void(0);" id="delete-event"
            data-toggle="tooltip" data-original-title="Delete"
             data-id="'.$event->id.'" class="px-3 py-1 border border-danger rounded mx-2 pr-3">
            <span class="fa fa-trash-alt text-danger" ></span></a>';

            $btn .= '<a href="javascript:void(0);" id="view-event"
            data-toggle="tooltip" data-original-title="View"
             data-id="'.$event->id.'" class="px-3 py-1 border border-secondary rounded text-secondary mx-2">
            <i class="fa fa-eye" ></i></a>';



           return $btn;

        })->addColumn('checkbox', function ($event) {
              $checkBox = '<input type="checkbox" id="'.$event->id.'"/>';
             return $checkBox;
        })->editColumn('start_time', function ($data) {
            return date('H:i', strtotime($data->start_time));
        })->rawColumns(['action', 'checkbox']);
    }

    
    public function query(Event $model)
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
            'title',
            // 'description',
            'start_date',
            'end_date',
            'start_time',
            'event_registra',
        ];
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename()
    {
        return 'Events_' . date('YmdHis');
    }
}
