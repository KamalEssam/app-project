<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMarketPlaceCategoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('market_place_category', function (Blueprint $table) {
            $table->increments('id');
            $table->string('ar_name')->nullable();
            $table->string('en_name')->nullable();
            $table->tinyInteger('is_active')->default(1);  // 1 => active, 2 => not active
            $table->string('image')->default('default.png');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('market_place_category');
    }
}
