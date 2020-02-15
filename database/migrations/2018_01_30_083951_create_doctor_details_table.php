<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDoctorDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('doctor_details', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('account_id')->nullable();
            $table->tinyInteger('max_hours_to_cancel_reservation')->default(4);
            $table->longText('en_bio')->nullable();
            $table->longText('ar_bio')->nullable();
            $table->unsignedInteger('speciality_id')->nullable();
            $table->longText('en_reservation_message')->nullable();
            $table->longText('ar_reservation_message')->nullable();
            $table->text('facebook')->nullable();
            $table->text('twitter')->nullable();
            $table->text('linkedin')->nullable();
            $table->text('youtube')->nullable();
            $table->text('googlepluse')->nullable();
            $table->text('instagram')->nullable();
            $table->text('website')->nullable();
            $table->double('min_fees')->default(0);
            $table->tinyInteger('restrict_visit')->default(0);
            $table->unsignedInteger('created_by')->nullable();
            $table->unsignedInteger('updated_by')->nullable();
            $table->timestamps();
        });
        Schema::table('doctor_details', function (Blueprint $table) {
            $table->foreign('speciality_id')
                ->references('id')
                ->on('specialities')
                ->onUpdate('cascade')
                ->onDelete('set null');
        });
        Schema::table('doctor_details', function (Blueprint $table) {
            $table->foreign('account_id')
                ->references('id')
                ->on('accounts')
                ->onUpdate('cascade')
                ->onDelete('cascade');
        });
        Schema::table('doctor_details', function (Blueprint $table) {
            $table->foreign('created_by')
                ->references('id')
                ->on('users')
                ->onUpdate('cascade')
                ->onDelete('set null');
        });
        Schema::table('doctor_details', function (Blueprint $table) {
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
        Schema::dropIfExists('doctor_details');
    }
}
