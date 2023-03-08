<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class DebtorsCustomersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        // SQL SERVER QUERY

        // DB::statement("CREATE VIEW debtors_customers
        // AS select c.name AS 'name', c.contact 
        // AS 'contact', sum(c.debt) AS 'debts' 
        // from customers as c group by c.name,c.contact order by
        //     sum(c.debt) desc offset 0 rows");

 // MYSQL SERVER QUERY

            // DB::statement("CREATE OR REPLACE VIEW `debtors_customers` AS select `name` AS `name`,
            // `contact` AS `contact`, sum(`debt`) AS `debts` 
            // from `customers` group by `name`,`contact` order by
            // sum(`debt`) desc");

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement('DROP VIEW IF EXISTS debtors_customers');
    }
}
