<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMedicationVisitTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('medication_visit', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('visit_id')->nullable();
            $table->unsignedInteger('medication_id')->nullable();
            $table->timestamps();
        });

        Schema::table('medication_visit', function (Blueprint $table) {
            $table->foreign('visit_id')
                ->references('id')
                ->on('visits')
                ->onUpdate('cascade')
                ->onDelete('cascade');
        });
        Schema::table('medication_visit', function (Blueprint $table) {
            $table->foreign('medication_id')
                ->references('id')
                ->on('medications')
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
        Schema::dropIfExists('medication_visit');
    }
}
