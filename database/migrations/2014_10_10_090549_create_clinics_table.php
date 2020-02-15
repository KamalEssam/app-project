<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateClinicsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('clinics', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('account_id');
            $table->longText('en_address')->nullable();
            $table->longText('ar_address')->nullable();
            $table->integer('avg_reservation_time')->default(15);
            $table->tinyInteger('reservation_deadline')->default(30);
            $table->double('lat')->nullable();
            $table->double('lng')->nullable();
            $table->double('fees')->nullable();
            $table->double('follow_up_fees')->nullable();
            $table->tinyInteger('vat_included')->default(0);   // 0 -> not included , 1 -> included
            $table->boolean('pattern')->default(0);// 0 =>intervals , 1=> queuing
            $table->integer('res_limit')->default(50); // max number of patients clinic apply
            $table->unsignedInteger('created_by')->nullable();
            $table->unsignedInteger('updated_by')->nullable();
            $table->timestamps();
        });

        Schema::table('clinics', function (Blueprint $table) {
            $table->foreign('account_id')
                ->references('id')
                ->on('accounts')
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
        Schema::dropIfExists('clinics');
    }
}
