<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class BestCashiersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        // SQL SERVER QUERY 

    //    DB::statement("CREATE VIEW top_cashiers AS
    //      select s.cashier AS 'cashier',sum(s.amount) AS 'totalsales'
    //     from sales as s group by s.cashier order by 
    //     sum(s.amount) desc offset 0 rows");

        // MYSQL SERVER QUERY

    //     DB::statement("CREATE OR REPLACE VIEW `top_cashiers` AS
    //     select `cashier` AS `cashier`,sum(`amount`) AS `totalsales`,
    //     round(((sum(`amount`) * 100) / (select sum(`monthlysales`.`TotalSales`)
    //     from `monthlysales`)),2) AS `percent` 
    //    from `sales` group by `cashier` order by sum(`amount`) desc");


        
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
         DB::statement('DROP VIEW IF EXISTS top_cashiers');
    }
}
