<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;


class CreateMonthlysalesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

                DB::statement("CREATE OR REPLACE VIEW monthlysales AS
                select 
                date_format(`date`,'%m-%Y') AS `month_year`, 
                year(`date`) AS SalesYear,
                month(`date`) AS month_int,
                monthname(`date`) AS SalesMonth, 
                sum(`amount`) AS TotalSales from `sales` 
                group by month_year,month_int, SalesMonth, SalesYear order by SalesYear desc");

}

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement('DROP VIEW IF EXISTS monthlysales');

    }
}
