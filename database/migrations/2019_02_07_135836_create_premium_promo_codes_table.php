<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePremiumPromoCodesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('premium_promo_codes', function (Blueprint $table) {
            $table->increments('id');
            $table->string('code');
            $table->unsignedInteger('influencer_id')->nullable();
            $table->unsignedInteger('owner_id')->nullable();
            $table->date('expiry_date')->nullable();
            $table->tinyInteger('discount_type')->default(1);   // 0=> amount of money , 1 => percentage
            $table->double('discount');
            $table->unsignedInteger('created_by')->nullable();
            $table->unsignedInteger('updated_by')->nullable();
            $table->timestamps();
        });

        Schema::table('premium_promo_codes', function (Blueprint $table) {
            $table->foreign('influencer_id')
                ->references('id')
                ->on('influencers')
                ->onUpdate('set null')
                ->onDelete('set null');
        });

        Schema::table('premium_promo_codes', function (Blueprint $table) {
            $table->foreign('owner_id')
                ->references('id')
                ->on('users')
                ->onUpdate('set null')
                ->onDelete('set null');
        });

        Schema::table('premium_promo_codes', function (Blueprint $table) {
            $table->foreign('created_by')
                ->references('id')
                ->on('users')
                ->onUpdate('cascade')
                ->onDelete('set null');
        });
        Schema::table('premium_promo_codes', function (Blueprint $table) {
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
        Schema::dropIfExists('premium_promo_codes');
    }
}
