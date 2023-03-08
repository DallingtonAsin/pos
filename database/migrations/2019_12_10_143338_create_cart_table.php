<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateCartTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {


//    DB::statement("CREATE TABLE cart(
//             id BIGINT PRIMARY KEY IDENTITY,
//             item_id NVARCHAR(255),
//             item NVARCHAR(255) NOT NULL,
//             quantity FLOAT NOT NULL,
//             price money NOT NULL,
//             total_cost AS quantity*price PERSISTED,
//             discount money,
//             amount money NOT NULL,
//             got_sold BIT DEFAULT 0,
//             created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
//             date_of_sale DATETIME, 
//          )");

        Schema::create('cart', function (Blueprint $table) {
            $table->id();
            $table->string('item_id')->nullable();
            $table->string('item');
            $table->double('quantity');
            $table->double('price');
            $table->double('total_cost')->storedAs('quantity * price');
            $table->double('discount');
            $table->double('amount');
            $table->boolean('got_sold')->default(0)->change();
            $table->dateTime('date_of_sale');
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
        // DB::statement('DROP TABLE IF EXISTS cart');
       Schema::dropIfExists('cart');
    }
}
