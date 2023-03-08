<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Query\Expression;

class CreateDamagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        
        // DB::statement("CREATE TABLE damages(
        //     id BIGINT PRIMARY KEY IDENTITY,
        //     item_id NVARCHAR(255),
        //     item NVARCHAR(255) NOT NULL,
        //     category NVARCHAR(255),
        //     quantity FLOAT NOT NULL,
        //     buying_price money NOT NULL,
        //     total_cost AS quantity*buying_price PERSISTED,
        //     recordedOn DATETIME DEFAULT CURRENT_TIMESTAMP,
        //  )");


        Schema::create('damages', function (Blueprint $table) {
            $table->id();
            $table->string('item_id')->nullable();
            $table->string('item');
            $table->string('category')->nullable();
            $table->double('quantity');
            $table->double('buying_price');
            $table->double('total_cost')->storedAs('quantity * buying_price')->nullable();
            $table->timestamp('recordedOn')->default(DB::raw('CURRENT_TIMESTAMP'));
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
        DB::statement('DROP TABLE IF EXISTS damages');
       // Schema::dropIfExists('damages');
    }
}
