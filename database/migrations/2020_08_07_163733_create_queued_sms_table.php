<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQueuedSmsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('queued_sms', function (Blueprint $table) {
            $table->id();
            $table->text('sender_telno');
            $table->string('receiver_telno');
            $table->text('message');
            $table->boolean('queued')->default(false);
            $table->boolean('sent')->default(false);
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
        Schema::dropIfExists('queued_sms');
    }
}
