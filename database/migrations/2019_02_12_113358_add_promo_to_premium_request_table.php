<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPromoToPremiumRequestTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('premium_request', function (Blueprint $table) {
            $table->unsignedInteger('promo_code_id')->nullable();
            $table->double('due_amount')->default(0);
        });

        Schema::table('premium_request', function (Blueprint $table) {
            $table->foreign('promo_code_id')
                ->references('id')
                ->on('premium_promo_codes')
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
        Schema::table('premium_request', function (Blueprint $table) {
            //
        });
    }
}
