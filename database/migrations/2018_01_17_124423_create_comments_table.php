<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCommentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('comments', function (Blueprint $table) {
            $table->increments('id');
            $table->longText('comment');
            $table->unsignedInteger('visit_id')->nullable();
            $table->unsignedInteger('created_by')->nullable();

            $table->timestamps();
        });
        Schema::table('comments', function (Blueprint $table) {
            $table->foreign('visit_id')
                ->references('id')
                ->on('visits')
                ->onUpdate('cascade')
                ->onDelete('cascade');
        });
        Schema::table('comments', function (Blueprint $table) {
            $table->foreign('created_by')
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
        Schema::dropIfExists('comments');
    }
}
