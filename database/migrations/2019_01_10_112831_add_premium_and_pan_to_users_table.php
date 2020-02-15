<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPremiumAndPanToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->tinyInteger('is_premium')->default(0);      // 0 => means regular user, 1 => means premium
            $table->unsignedInteger('user_plan_id')->nullable();
            $table->date('expiry_date')->nullable();
        });


        Schema::table('users', function (Blueprint $table) {

            $table->foreign('user_plan_id')
                ->references('id')
                ->on('patients_plans')
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
        Schema::table('users', function (Blueprint $table) {
            //
        });
    }
}
