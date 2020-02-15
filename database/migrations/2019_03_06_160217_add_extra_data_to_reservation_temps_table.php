<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddExtraDataToReservationTempsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('reservation_temps', function (Blueprint $table) {
            $table->text('device_token');
            $table->unsignedInteger('doctor_id')->nullable();
            $table->unsignedInteger('clinic_id')->nullable();
        });

        Schema::table('reservation_temps', function (Blueprint $table) {
            $table->foreign('doctor_id')
                ->references('id')
                ->on('users')
                ->onUpdate('cascade')
                ->onDelete('cascade');
        });

        Schema::table('reservation_temps', function (Blueprint $table) {
            $table->foreign('clinic_id')
                ->references('id')
                ->on('clinics')
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
        Schema::table('reservation_temps', function (Blueprint $table) {
            //
        });
    }
}
