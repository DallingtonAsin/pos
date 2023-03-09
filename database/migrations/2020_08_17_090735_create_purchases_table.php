<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CreatePurchasesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::create('purchases', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('item_id');
            $table->float('quantity');
            $table->double('cost_price_per_item');
            $table->double('total_cost_price')->storedAs('quantity * cost_price_per_item');
            $table->double('retail_price');
            $table->double('wholesale_price')->nullable();
            $table->unsignedBigInteger('supplier_id')->nullable();
            $table->string('serial_no')->nullable();
            $table->string('receipt_no')->nullable();
            $table->unsignedBigInteger('recorded_by');
            $table->date('date_of_purchase')->nullable();
            $table->dateTime('date')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamps();

            $table->foreign('item_id')->references('id')->on('stock')->onDelete('cascade');
            $table->foreign('supplier_id')->references('id')->on('suppliers')->onDelete('cascade');
            $table->foreign('recorded_by')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('purchases');
        // DB::statement("DROP TABLE IF EXISTS purchases");
    }
}
