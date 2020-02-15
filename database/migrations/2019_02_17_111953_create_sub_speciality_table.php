<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSubSpecialityTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sub_specialities', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('speciality_id')->nullable();
            $table->string('ar_name')->nullable();
            $table->string('en_name')->nullable();
            $table->timestamps();
        });

        Schema::table('sub_specialities', function (Blueprint $table) {
            $table->foreign('speciality_id')
                ->references('id')
                ->on('specialities')
                ->onUpdate('set null')
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sub_speciality');
    }
}
