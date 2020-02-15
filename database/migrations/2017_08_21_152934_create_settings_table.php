<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->increments('id');
            $table->string('website')->nullable();
            $table->bigInteger('user_counter')->default(0);
            $table->bigInteger('assistant_counter')->default(0);
            $table->bigInteger('account_counter')->default(0);
            $table->unsignedInteger('min_featured_stars')->default(10);
            $table->string('facebook')->nullable();
            $table->string('twitter')->nullable();
            $table->string('mobile')->nullable();
            $table->string('youtube')->nullable();
            $table->string('googlepluse')->nullable();
            $table->string('instagram')->nullable();

            $table->string('email')->nullable();

            $table->text('en_about_us')->nullable();
            $table->text('ar_about_us')->nullable();

            $table->unsignedInteger('created_by')->nullable();
            $table->unsignedInteger('updated_by')->nullable();
            $table->timestamps();
        });
        Schema::table('settings', function (Blueprint $table) {
            $table->foreign('created_by')
                ->references('id')
                ->on('users')
                ->onUpdate('cascade')
                ->onDelete('set null');
        });
        Schema::table('settings', function (Blueprint $table) {
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
        Schema::dropIfExists('settings');
    }
}
