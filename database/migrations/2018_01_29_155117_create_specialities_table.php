<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSpecialitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('specialities', function (Blueprint $table) {
            $table->increments('id');
            $table->string('en_speciality');
            $table->string('ar_speciality');

            $table->text('en_desc')->nullable(); // speciality description
            $table->text('ar_desc')->nullable();
            $table->tinyInteger('is_featured')->default(0);   // only five specialities can be featured

            $table->string('slug')->nullable();
            $table->string('image')->default('default.png');
            $table->string('icon')->default('default.png');

            $table->unsignedInteger('created_by')->nullable();
            $table->unsignedInteger('updated_by')->nullable();
            $table->timestamps();
        });
        Schema::table('specialities', function (Blueprint $table) {
            $table->foreign('created_by')
                ->references('id')
                ->on('users')
                ->onUpdate('cascade')
                ->onDelete('set null');
        });
        Schema::table('specialities', function (Blueprint $table) {
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
        Schema::dropIfExists('specialities');
    }
}
