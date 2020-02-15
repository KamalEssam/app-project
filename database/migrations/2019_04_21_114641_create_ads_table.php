<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAdsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ads', function (Blueprint $table) {
            $table->increments('id');

            $table->string('ar_title')->nullable();
            $table->string('en_title')->nullable();

            $table->text('en_desc')->nullable();
            $table->text('ar_desc')->nullable();

            $table->string('screen_shot')->default('default.png');
            $table->string('background')->default('default.png');

            $table->tinyInteger('type')->default(0);  // 0 => offer , 1 => doctor

            $table->unsignedMediumInteger('clicks')->default(0);        // medium integer unsigned max is 16 million

            $table->unsignedInteger('offer_id')->nullable();
            $table->unsignedInteger('doctor_id')->nullable();

            $table->tinyInteger('is_active')->default(1);

            $table->time('time_from')->nullable();
            $table->time('time_to')->nullable();

            $table->date('date_from')->nullable();
            $table->date('date_to')->nullable();

            $table->tinyInteger('slide')->default(1);
            $table->smallInteger('priority')->default(1);   // priority of ad

            $table->timestamps();
        });

        Schema::table('ads', function (Blueprint $table) {
            $table->foreign('offer_id')
                ->references('id')
                ->on('offers')
                ->onUpdate('cascade')
                ->onDelete('cascade');
        });

        Schema::table('ads', function (Blueprint $table) {
            $table->foreign('doctor_id')
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
        Schema::dropIfExists('ads');
    }
}
