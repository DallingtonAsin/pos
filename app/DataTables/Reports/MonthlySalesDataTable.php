<?php

namespace App\DataTables\Reports;

use App\User;
use Yajra\DataTables\Services\DataTable;
use App\Models\MonthlySale;
use App\Models\MonthlyPurchase;
use App\Helpers\Helper;


class MonthlySalesDataTable extends DataTable
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
        ->addColumn('period', function ($data){
            $month = date("F", mktime(0, 0, 0, $data->month_int, 10)); 
            return $month ." ".$data->SalesYear;
        })->editColumn('month', function ($data){
            $month = date("F", mktime(0, 0, 0, $data->month_int, 10)); 
            return $month;
        })->editColumn('year', function ($data){
            return $data->SalesYear;
        })->addColumn('sales', function ($data){
            return number_format($data->TotalSales);
        })->addColumn('percent', function ($data){
             $total  = MonthlySale::sum('TotalSales');
             return round(($data->TotalSales/$total)*100, 2);
         })->addColumn('profits', function ($data){
            $profits = Helper::getProfitsForAGivenMonth($data->SalesYear, $data->month_int);
            $profits = number_format($profits);
            return $profits;
        });
    }

 



    /**
     * Get query source of dataTable.
     *
     * @param \App\Model\MonthlySale $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(MonthlySale $model)
    {
        $collection = collect();
        $monthly_sales = MonthlySale::orderBy('SalesYear', 'desc');
        $monthly_sales = $monthly_sales->orderBy('month_int', 'desc');
        $monthly_sales = $monthly_sales->get();
        foreach($monthly_sales as $item){
            $total_purchases = MonthlyPurchase::where('purchase_year', $item->SalesYear)->where('month_int', $item->month_int)->value('total_purchases');
         
            if(!empty($total_purchases)){
              $item->TotalPurchases = number_format($total_purchases);
            }else{
                $item->TotalPurchases = 0;
            }
        }
         return $monthly_sales;
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
        return [];
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename()
    {
        return 'Reports\MonthlySales_' . date('YmdHis');
    }
}
