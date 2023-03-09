<?php

use Illuminate\Database\Migrations\Migration;
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
