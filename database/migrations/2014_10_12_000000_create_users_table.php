<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->nullable();
            $table->string('email')->nullable();
            $table->string('password')->nullable();
            $table->boolean('gender')->nullable();  // 0 for male ,1 for female
            $table->string('mobile');
            $table->date('birthday')->nullable();
            $table->longText('address')->nullable();

            $table->unsignedInteger('role_id')->nullable(); //1 for doctor, 2 for assistant, 3 for user

            $table->string('facebook_id')->nullable();
            $table->string('google_id')->nullable();

            $table->boolean('is_active')->default(0);
            $table->boolean('is_notification')->default(1);

            $table->dateTime('last_notification_click')->nullable();

            $table->string('image')->default('default.png');   // dont ever change the default name (default.png)
            $table->string('unique_id')->unique()->nullable();
            $table->unsignedInteger('account_id')->nullable();
            $table->unsignedInteger('clinic_id')->nullable();

            $table->double('height')->nullable();
            $table->double('weight')->nullable();

            $table->tinyInteger('percentage')->default(4);

            $table->string('pin')->nullable();
            $table->string('lang')->default('en');

            $table->unsignedInteger('created_by')->nullable();
            $table->unsignedInteger('updated_by')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });
        Schema::table('users', function (Blueprint $table) {
            $table->foreign('account_id')
                ->references('id')
                ->on('accounts')
                ->onUpdate('cascade')
                ->onDelete('cascade');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->foreign('role_id')
                ->references('id')
                ->on('roles')
                ->onUpdate('cascade')
                ->onDelete('cascade');

        });
        Schema::table('users', function (Blueprint $table) {
            $table->foreign('clinic_id')
                ->references('id')
                ->on('clinics')
                ->onUpdate('cascade')
                ->onDelete('cascade');
        });
        Schema::table('users', function (Blueprint $table) {
            $table->foreign('created_by')
                ->references('id')
                ->on('users')
                ->onUpdate('cascade')
                ->onDelete('set null');
        });
        Schema::table('users', function (Blueprint $table) {
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
        Schema::dropIfExists('users');
    }
}
