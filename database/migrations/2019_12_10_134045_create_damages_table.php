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

        Schema::create('damages', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('item_id');
            $table->double('quantity');
            $table->timestamp('recorded_on')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->unsignedBigInteger('recorded_by');
            $table->timestamps();

            $table->foreign('item_id')->references('id')->on('stock')->onDelete('cascade');
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
        DB::statement('DROP TABLE IF EXISTS damages');
        // Schema::dropIfExists('damages');
    }
}
