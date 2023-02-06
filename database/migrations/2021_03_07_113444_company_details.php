<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CompanyDetails extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('company_details', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('abbrev')->nullable();
                $table->string('phone_number')->unique();
                $table->string('email')->unique();
                $table->string('address');
                $table->string('logo')->nullable();
                $table->boolean('is_registered')->default(false);
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
        Schema::dropIfExists('company_details');
    }
}
