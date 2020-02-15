<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOffersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('offers', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('category_id');
            $table->string('ar_name');
            $table->string('en_name');
            $table->text('ar_desc');
            $table->text('en_desc');
            $table->unsignedInteger('doctor_id');
            $table->tinyInteger('reservation_fees_included')->default(0);    // means if the reservation is Free
            $table->double('old_price')->default(0);
            $table->double('new_price')->default(0);
            $table->unsignedInteger('views_no');
            $table->unsignedInteger('users_booked');
            $table->tinyInteger('is_featured')->default(0);
            $table->string('image')->default('default.png');
            $table->date('expiry_date')->nullable();
            $table->unsignedInteger('created_by')->nullable();
            $table->unsignedInteger('updated_by')->nullable();
            $table->timestamps();
        });

        Schema::table('offers', function (Blueprint $table) {
            $table->foreign('doctor_id')
                ->references('id')
                ->on('users')
                ->onUpdate('cascade')
                ->onDelete('cascade');

        });


        Schema::table('offers', function (Blueprint $table) {
            $table->foreign('created_by')
                ->references('id')
                ->on('users')
                ->onUpdate('cascade')
                ->onDelete('cascade');

        });
        Schema::table('offers', function (Blueprint $table) {
            $table->foreign('updated_by')
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
        Schema::dropIfExists('offers');
    }
}
