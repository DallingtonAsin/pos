<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateTopCustomersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        //SQL SERVER QUERY

        // DB::statement("CREATE VIEW top_customers AS
        // select TOP 10 s.customer AS 'customer',sum(s.amount) AS 'volumeofsales'
        // from sales as s group by
        // s.customer order by sum(s.amount) desc");

        // MYSQL SERVER 

        // DB::statement("CREATE OR REPLACE VIEW `top_customers` AS
        // select `customer` AS `customer`, sum(`amount`) AS `volumeofsales`,
        // round(((sum(`amount`) * 100) / (select sum(`monthlysales`.`TotalSales`) 
        // from `monthlysales`)),2)
        // AS `percent` from `sales` group by `customer` order by sum(`amount`) desc limit 10");
   
}
    
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
         DB::statement('DROP VIEW IF EXISTS top_customers');
    }
}
