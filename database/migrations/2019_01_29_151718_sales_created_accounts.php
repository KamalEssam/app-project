<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class SalesCreatedAccounts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sales_created_accounts', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('sales_id')->nullable();
            $table->unsignedInteger('account_id')->nullable();
            $table->timestamps();
        });

        Schema::table('sales_created_accounts', function (Blueprint $table) {
            $table->foreign('account_id')
                ->references('id')
                ->on('accounts')
                ->onUpdate('cascade')
                ->onDelete('cascade');

        });

        Schema::table('sales_created_accounts', function (Blueprint $table) {
            $table->foreign('sales_id')
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
        Schema::dropIfExists('sales_created_accounts');
    }
}
