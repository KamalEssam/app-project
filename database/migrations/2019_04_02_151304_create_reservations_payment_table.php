<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateReservationsPaymentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reservations_payment', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('reservation_id');
            $table->unsignedInteger('offer_id')->nullable();
            $table->double('fees')->default(0);
            $table->double('premium_fees')->default(0);
            $table->tinyInteger('patient_premium')->default(0);
            $table->tinyInteger('doctor_premium')->default(0);
            $table->tinyInteger('vat_included')->default(0);
            $table->timestamps();
        });

        Schema::table('reservations_payment', function (Blueprint $table) {
            $table->foreign('offer_id')
                ->references('id')
                ->on('offers')
                ->onUpdate('cascade')
                ->onDelete('cascade');
        });

        Schema::table('reservations_payment', function (Blueprint $table) {
            $table->foreign('reservation_id')
                ->references('id')
                ->on('reservations')
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
        Schema::dropIfExists('reservations_payment');
    }
}
