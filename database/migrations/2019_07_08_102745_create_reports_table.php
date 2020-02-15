<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateReportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reports', function (Blueprint $table) {
            $table->increments('id');
            $table->text('body')->nullable();   // report description
            $table->unsignedInteger('user_id')->nullable();  //
            $table->unsignedInteger('reservation_id')->nullable();  //
            $table->tinyInteger('type')->nullable();   // get from config
            $table->tinyInteger('status')->default(0);  // pending => 0, processing => 1, handled => 2
            $table->timestamps();
        });

        Schema::table('reports', function (Blueprint $table) {
            $table->foreign('reservation_id')
                ->references('id')
                ->on('reservations')
                ->onUpdate('cascade')
                ->onDelete('cascade');
        });

        Schema::table('reports', function (Blueprint $table) {
            $table->foreign('user_id')
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
        Schema::dropIfExists('reports');
    }
}
