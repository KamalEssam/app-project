<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateReservationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reservations', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id')->nullable();
            $table->unsignedInteger('working_hour_id')->nullable();
            $table->unsignedInteger('clinic_id')->nullable();
            $table->integer('queue')->default(0);
            $table->tinyInteger('status')->default(0);// 0 -> pending , 1->approved , 2->canceled, 3->attended , 4->missed
            $table->tinyInteger('type')->default(0); // 0->check up , 1 ->follow up
            $table->date('day')->nullable();
            $table->time('check_in')->nullable();
            $table->time('check_out')->nullable();
            $table->longText('complaint')->nullable();
            $table->unsignedInteger('created_by')->nullable();
            $table->unsignedInteger('updated_by')->nullable();
            $table->tinyInteger('payment_method')->default(0); // 0 -> cash , 1 -> credit, 2 -> installment
            $table->string('transaction_id')->default(-1); //  -1 =>cash and not paid , -2 => cash and paid , otherwise=> normal transaction
            $table->timestamps();
        });

        Schema::table('reservations', function (Blueprint $table) {
            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onUpdate('cascade')
                ->onDelete('cascade');

        });
        Schema::table('reservations', function (Blueprint $table) {
            $table->foreign('working_hour_id')
                ->references('id')
                ->on('working_hours')
                ->onUpdate('cascade')
                ->onDelete('cascade');

        });
        Schema::table('reservations', function (Blueprint $table) {
            $table->foreign('clinic_id')
                ->references('id')
                ->on('clinics')
                ->onUpdate('cascade')
                ->onDelete('cascade');

        });
        Schema::table('reservations', function (Blueprint $table) {
            $table->foreign('created_by')
                ->references('id')
                ->on('users')
                ->onUpdate('cascade')
                ->onDelete('set null');
        });
        Schema::table('reservations', function (Blueprint $table) {
            $table->foreign('updated_by')
                ->references('id')
                ->on('users')
                ->onUpdate('cascade')
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
        Schema::dropIfExists('reservations');
    }
}
