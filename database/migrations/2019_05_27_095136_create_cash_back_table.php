<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCashBackTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cash_back', function (Blueprint $table) {
            $table->increments('id');

            $table->unsignedInteger('account_id')->nullable();
            $table->unsignedInteger('patient_id')->nullable();
            $table->unsignedInteger('clinic_id')->nullable();
            $table->unsignedInteger('reservation_id')->nullable();
            $table->tinyInteger('is_approved')->default(0);    // 0 => pending,  1 => approved,  -1 => dis approved
            $table->double('patient_cash')->nullable();
            $table->double('doctor_cash')->nullable();
            $table->double('seena_cash')->nullable();
            $table->tinyInteger('is_paid')->default(0);      // 0 => not paid,  1 => paid

            $table->timestamps();
        });

        Schema::table('cash_back', function (Blueprint $table) {
            $table->foreign('account_id')
                ->references('id')
                ->on('accounts')
                ->onUpdate('set null')
                ->onDelete('set null');
        });


        Schema::table('cash_back', function (Blueprint $table) {
            $table->foreign('patient_id')
                ->references('id')
                ->on('users')
                ->onUpdate('set null')
                ->onDelete('set null');
        });

        Schema::table('cash_back', function (Blueprint $table) {
            $table->foreign('clinic_id')
                ->references('id')
                ->on('clinics')
                ->onUpdate('set null')
                ->onDelete('set null');
        });

        Schema::table('cash_back', function (Blueprint $table) {
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
        Schema::dropIfExists('cash_back');
    }
}
