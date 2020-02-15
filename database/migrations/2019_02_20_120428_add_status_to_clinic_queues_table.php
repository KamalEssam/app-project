<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddStatusToClinicQueuesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('clinic_queues', function (Blueprint $table) {
            $table->tinyInteger('queue_status')->default(1)->after('queue'); // 0 => closed ; 1 =>opend
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('clinic_queues', function (Blueprint $table) {
            //
        });
    }
}
