<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSpecialityIdToOfferCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('offers_categories', function (Blueprint $table) {
            $table->unsignedInteger('speciality_id')->nullable();
        });

        Schema::table('offers_categories', function (Blueprint $table) {
            $table->foreign('speciality_id')
                ->references('id')
                ->on('specialities')
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
        Schema::table('offer_categories', function (Blueprint $table) {
            $table->dropColumn('speciality_id');
        });
    }
}
