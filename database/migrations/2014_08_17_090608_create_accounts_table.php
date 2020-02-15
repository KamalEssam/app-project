<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAccountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('accounts', function (Blueprint $table) {
            $table->increments('id');
            $table->string('en_name')->default('no name');
            $table->string('ar_name')->default('لا يوجد اسم');
            $table->string('unique_id')->nullable()->unique();
            $table->double('due_amount')->nullable();
            $table->date('due_date')->nullable();
            $table->unsignedInteger('city_id')->nullable();
            $table->unsignedInteger('plan_id')->nullable();
            $table->tinyInteger('is_published')->default(0); // 0 => no clinics , 1=> have clinics
            $table->unsignedInteger('no_of_views')->default(0); // how many patient view this account
            $table->unsignedInteger('created_by')->nullable();
            $table->unsignedInteger('updated_by')->nullable();
            $table->timestamps();
        });

        Schema::table('accounts', function (Blueprint $table) {
            $table->foreign('plan_id')
                ->references('id')
                ->on('plans')
                ->onUpdate('cascade')
                ->onDelete('cascade');
        });

        Schema::table('accounts', function (Blueprint $table) {
            $table->foreign('city_id')
                ->references('id')
                ->on('cities')
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
        Schema::dropIfExists('accounts');
    }
}
