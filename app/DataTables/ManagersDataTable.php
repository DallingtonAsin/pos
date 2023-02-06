<?php

namespace App\DataTables;

use Yajra\DataTables\Services\DataTable;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\User;
use App\Helpers\Helper;

class ManagersDataTable extends DataTable
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
        ->addColumn('action', function ($user) {
            
            $btn = '<a href="javascript:void(0)" data-toggle="tooltip" 
            data-id="'.$user->id.'" data-original-title="Edit" id="edit-user"
              class="px-3 py-1 border border-success rounded mx-2 edit-user pr-4">
             <span class="fa fa-pen text-success"></span></a>';
          
            $btn .= '<a href="javascript:void(0);" id="delete-user" 
            data-toggle="tooltip" data-original-title="Delete"
             data-id="'.$user->id.'" class="px-3 py-1 border border-danger rounded mx-2 pr-4"">
            <span class="fa fa-trash-alt text-danger" ></span></a>';

           $btn .= '<a href="javascript:void(0);" id="view-user" 
           data-toggle="tooltip" data-original-title="View"
            data-id="'.$user->id.'" class="px-3 py-1 border border-secondary rounded text-secondary mx-2">
           <i class="fa fa-eye" ></i></a>';

           return $btn;

        })->addColumn('checkbox', function ($user) {
              $checkBox = '<input type="checkbox" id="'.$user->id.'"/>';
             return $checkBox;
        })->editColumn('isActive', function ($data) {
           return ($data->isActive)
             ? '<span class="text-success">active</span>' 
             : '<span class="text-danger">inactive</span>';
        })->editColumn('accountAction', function ($data) {
            $status = $data->isActive;
           return ($status)
             ? '<button class="bg-danger py-0 text-white changeAccountBtn" data-id="'.$data->id.'" data-name="'.$data->name.'" data-status="'.$status.'" id="changeAccountBtn" style="font-size: 0.8em;"  >Deactive</button>' 
             : '<button class="bg-success py-0 text-white changeAccountBtn" data-id="'.$data->id.'" data-name="'.$data->name.'"  data-status="'.$status.'" id="changeAccountBtn" style="font-size: 0.8em;" >Activate</button>';
        })->rawColumns(['action', 'isActive', 'accountAction', 'checkbox']);


    }

    public function query(User $model)
    {
               $adminRoleId = Helper::getRoleId('Administrator');
               $res = $model->newQuery()
               ->select('*')
               ->where('user_role', '=', intval($adminRoleId))
               ->where('id', '!=', Auth::user()->id);
               return $res;
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
            'first_name',
            'last_name',
            'name',
            'username',
            'gender',
            'email',
            'user_role',
            'tel_no',
            'alt_telno',
            'address',
            'nationalID_no',
            'image',
            'password'
        ];
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename()
    {
        return 'users' . date('YmdHis');
    }
}

