<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeReservationPaymentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('reservations_payment', function (Blueprint $table) {
            // columns to be removed
            $table->dropForeign('reservations_payment_offer_id_foreign');
            $table->dropColumn('offer_id');
            $table->dropColumn('premium_fees');
            $table->dropColumn('patient_premium');
            $table->dropColumn('doctor_premium');
            $table->dropColumn('vat_included');

            // columns to be added
            $table->double('offer')->nullable();
            $table->double('promo')->nullable();
            $table->double('discount')->nullable();
            $table->double('total')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
