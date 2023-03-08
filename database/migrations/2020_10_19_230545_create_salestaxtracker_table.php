<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSalestaxtrackerTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('salestaxtracker', function (Blueprint $table) {
          $table->id();
          $table->string('item_id', 255);
          $table->string('item', 255);
          $table->string('quantity', 255);
          $table->string('amount', 255);
          $table->string('tax', 255);
          $table->string('date_of_sale', 255);
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
        Schema::dropIfExists('salestaxtracker');
    }
}
