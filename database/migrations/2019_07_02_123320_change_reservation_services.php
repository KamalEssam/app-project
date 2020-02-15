<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeReservationServices extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('reservation_services', function (Blueprint $table) {
            // columns to be removed
            $table->dropForeign('reservation_services_account_service_id_foreign');
            $table->dropColumn('account_service_id');

            // columns to be added
            $table->string('ar_name')->nullable();
            $table->string('en_name')->nullable();
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
