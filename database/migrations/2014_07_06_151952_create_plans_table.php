<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePlansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('plans', function (Blueprint $table) {
            $table->increments('id');
            $table->string('en_name');
            $table->string('ar_name');
            $table->double('price_of_day');
            $table->unsignedInteger('no_of_clinics')->default(3);  // 3 for Doctors and 0 for Centers means unlimited
            $table->longText('en_desc');
            $table->longText('ar_desc');
            $table->string('image')->default('default.png');
            $table->unsignedInteger('created_by')->nullable();
            $table->unsignedInteger('updated_by')->nullable();
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
        Schema::dropIfExists('plans');
    }
}
