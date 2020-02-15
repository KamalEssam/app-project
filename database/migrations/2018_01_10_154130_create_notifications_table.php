<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNotificationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('sender_id')->nullable();
            $table->unsignedInteger('receiver_id')->nullable();
            $table->boolean('multicast')->default(0); // 0 => one person  ay rakm tany 3ala 7asb el role
            $table->string('en_title')->nullable();
            $table->string('ar_title')->nullable();
            $table->string('en_message')->nullable();
            $table->string('ar_message')->nullable();
            $table->string('url')->nullable();
            $table->unsignedInteger('object_id')->nullable();
            $table->string('table')->nullable(); // any table elly hangeb mno el 7agat bta3t el notification
            $table->boolean('is_read')->default(0);

            $table->unsignedInteger('created_by')->nullable();
            $table->timestamps();
        });
        Schema::table('notifications', function (Blueprint $table) {
            $table->foreign('created_by')
                ->references('id')
                ->on('users')
                ->onUpdate('cascade')
                ->onDelete('set null');
        });

        // the receiver id  might be user id or clinic_id so cant be related
//        Schema::table('notifications', function (Blueprint $table) {
//            $table->foreign('receiver_id')
//                ->references('id')
//                ->on('users')
//                ->onUpdate('cascade')
//                ->onDelete('cascade');
//        });
        Schema::table('notifications', function (Blueprint $table) {
            $table->foreign('sender_id')
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
        Schema::dropIfExists('notifications');
    }
}
