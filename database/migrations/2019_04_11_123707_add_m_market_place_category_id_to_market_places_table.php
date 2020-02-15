<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddMMarketPlaceCategoryIdToMarketPlacesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('market_places', function (Blueprint $table) {
            $table->unsignedInteger('market_place_category_id')->nullable();
        });

        Schema::table('market_places', function (Blueprint $table) {
            $table->foreign('market_place_category_id')
                ->references('id')
                ->on('market_place_category')
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
        Schema::table('market_places', function (Blueprint $table) {
            $table->dropColumn('market_place_category_id');
        });
    }
}
