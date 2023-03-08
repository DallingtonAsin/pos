<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Query\Expression;
use Carbon\Carbon;

class CreateStockTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        // DB::statement("CREATE TABLE stock(
        //    id BIGINT PRIMARY KEY IDENTITY,
        //    item_code NVARCHAR(255),
        //    item NVARCHAR(255),
        //    category NVARCHAR(255),
        //    quantity FLOAT NOT NULL,
        //    threshold_qty FLOAT DEFAULT 0,
        //    buying_price money NOT NULL,
        //    selling_price money NOT NULL,
        //    profit_per_item AS selling_price-buying_price PERSISTED,
        //    total_cost_price AS quantity*buying_price PERSISTED,
        //    total_profit AS quantity*(selling_price-buying_price) PERSISTED,
        //    supplier NVARCHAR(255),
        //    date_of_entry DATETIME DEFAULT CURRENT_TIMESTAMP,
        //    expiry_date DATE,
        // )");

        Schema::create('stock', function (Blueprint $table) {
            $table->id();
            $table->string('item_code')->unique()->nullable();
            $table->string('item');
            $table->string('category')->nullable();
            $table->double('quantity');
            $table->float('threshold_qty')->default('0');
            $table->double('buying_price');
            $table->double('selling_price');
            $table->double('wholesale_price')->default('0');
            $table->double('profit_per_item')->storedAs('selling_price-buying_price')->nullable();
            $table->double('total_cost_price')->storedAs('quantity*buying_price')->nullable();
            $table->double('total_profit')->storedAs('quantity*(selling_price-buying_price)')->nullable();
            $table->string('supplier')->nullable();
            $table->timestamp('date_of_entry')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->date('expiry_date')->nullable();
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
        // DB::statement("DROP TABLE IF EXISTS stock");
       Schema::dropIfExists('stock');
       
    }
}
