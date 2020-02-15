<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateReservationServicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reservation_services', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('reservation_id')->nullable();
            $table->unsignedInteger('account_service_id')->nullable();
            $table->double('price')->nullable();
            $table->timestamps();
        });

        Schema::table('reservation_services', function (Blueprint $table) {
            $table->foreign('reservation_id')
                ->references('id')
                ->on('reservations')
                ->onUpdate('cascade')
                ->onDelete('cascade');
        });

        Schema::table('reservation_services', function (Blueprint $table) {
            $table->foreign('account_service_id')
                ->references('id')
                ->on('account_service')
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
        Schema::dropIfExists('reservation_services');
    }
}
