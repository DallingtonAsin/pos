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

        // DB::statement("CREATE TABLE purchases(
        //     id BIGINT PRIMARY KEY IDENTITY,
        //     item_code NVARCHAR(255),
        //     item NVARCHAR(255),
        //     quantity FLOAT NOT NULL,
        //     cost_price_per_item money NOT NULL,
        //     total_cost_price AS quantity*cost_price_per_item PERSISTED,
        //     supplier NVARCHAR(255),
        //     recorded_by NVARCHAR(40),
        //     date DATETIME DEFAULT CURRENT_TIMESTAMP,
        //  )");

        Schema::create('purchases', function (Blueprint $table) {
            $table->id();
            $table->string('serial_no')->nullable();
            $table->string('receipt_no')->nullable();
            $table->string('item_code')->nullable();
            $table->string('item')->nullable();
            $table->float('quantity');
            $table->double('cost_price_per_item');
            $table->double('total_cost_price')->storedAs('quantity * cost_price_per_item');
            $table->double('retail_price')->default('0');
            $table->double('wholesale_price')->default('0');
            $table->string('supplier')->nullable();
            $table->string('supplier_contact')->nullable();
            $table->string('recorded_by');
            $table->date('date_of_purchase')->nullable();
            $table->dateTime('date')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamps();

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
