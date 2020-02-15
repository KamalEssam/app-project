<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateClinicQueuesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('clinic_queues', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('clinic_id')->nullable();
            $table->integer('queue')->default(0);
            $table->timestamps();
        });
        Schema::table('clinic_queues', function (Blueprint $table) {
            $table->foreign('clinic_id')
                ->references('id')
                ->on('clinics')
                ->onUpdate('cascade')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('clinic_queues');
    }
}
