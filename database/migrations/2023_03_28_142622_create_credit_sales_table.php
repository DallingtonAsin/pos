<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCreditSalesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('credit_sales', function (Blueprint $table) {
            $table->id();
            $table->string('sale_order_number')->nullable();
            $table->unsignedBigInteger('customer_id');
            $table->date('date');
            $table->double('total_cost');
            $table->double('amount_paid')->default(0);
            $table->double('amount_due');
            $table->timestamps();

            $table->foreign('sale_order_number')->references('order_number')->on('sales')->onDelete('cascade');
            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('credit_sales');
    }
}
