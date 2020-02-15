<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePremiumPromoCodesUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('premium_promo_codes_users', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id')->nullable();
            $table->unsignedInteger('influencer_id')->nullable();
            $table->timestamps();
        });


        Schema::table('premium_promo_codes_users', function (Blueprint $table) {
            $table->foreign('influencer_id')
                ->references('id')
                ->on('influencers')
                ->onUpdate('set null')
                ->onDelete('set null');
        });

        Schema::table('premium_promo_codes_users', function (Blueprint $table) {
            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onUpdate('set null')
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
        Schema::dropIfExists('premium_promo_codes_users');
    }
}
