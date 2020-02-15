<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMarketPlacesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('market_places', function (Blueprint $table) {
            $table->increments('id');
            $table->string('ar_title')->nullable();
            $table->string('en_title')->nullable();
            $table->text('ar_desc')->nullable();
            $table->text('en_desc')->nullable();
            $table->unsignedInteger('points');
            $table->string('image')->default('default.png');
            $table->tinyInteger('is_active')->default('1');  // 1 => active
            $table->unsignedInteger('max_redeems')->default(0);
            $table->unsignedInteger('brand_id')->nullable();
            $table->unsignedInteger('redeem_expiry_days')->default(0);

            $table->timestamps();
        });

        Schema::table('market_places', function (Blueprint $table) {
            $table->foreign('brand_id')
                ->references('id')
                ->on('users')
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
        Schema::dropIfExists('market_places');
    }
}
