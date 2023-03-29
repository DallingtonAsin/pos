<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->dropColumn('is_credit');
            $table->dropColumn('fully_paid');
            $table->dropColumn('balance');
            $table->dropColumn('extra_money');
            $table->dropColumn('paid_amount');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->boolean('is_credit');
            $table->boolean('fully_paid');
            $table->double('balance');
            $table->double('extra_money');
            $table->dropColumn('paid_amount');
        });
    }
};
