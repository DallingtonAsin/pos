<?php

namespace App\DataTables;

use Yajra\DataTables\Services\DataTable;
use App\Models\Company;

class CompanyDataTable extends DataTable
{

    /**
     * Build DataTable class.
     *
     * @param mixed $query Results from query() method.
     * @return \Yajra\DataTables\DataTableAbstract
     */

    public function dataTable($query)
    {
        return datatables($query)->addIndexColumn()->addColumn('action', function ($company) {
            
            $btn = '<a href="javascript:void(0)" data-toggle="tooltip" 
            data-id="'.$company->id.'" data-original-title="Edit" id="edit-company"
              class="px-3 py-1 border border-success rounded mx-2 edit-company">
             <span class="glyphicon glyphicon-pencil"></span></a>';
          
            $btn .= '<a href="javascript:void(0);" id="delete-company" 
            data-toggle="tooltip" data-original-title="Delete"
             data-id="'.$company->id.'" class="px-3 py-1 border border-danger rounded mx-2 pl-4"">
            <span class="glyphicon glyphicon-trash" ></span></a>';

           $btn .= '<a href="javascript:void(0);" id="view-company" 
           data-toggle="tooltip" data-original-title="View"
            data-id="'.$company->id.'" class="px-3 py-1 border border-secondary rounded text-secondary mx-2 pl-4">
           <i class="fa fa-eye" ></i></a>';

           return $btn;

        });
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\User $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(Company $model)
    {
        return $model->newQuery()->select( 'id','company_name', 'company_abbrev',
                                           'company_email', 'company_address','company_motto','company_logo',
                                           'created_at', 'updated_at');
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
            'company_name',
            'company_abbrev',
            'company_email',
            'company_address',
            'company_motto',
            'company_logo',
            'created_at',
            'updated_at'
        ];
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename()
    {
        return 'Company_' . date('YmdHis');
    }
}
