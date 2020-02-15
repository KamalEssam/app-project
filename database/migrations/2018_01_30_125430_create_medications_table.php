<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMedicationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('medications', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->nullable()->default(null);
            $table->string('company')->nullable()->default(null);
            $table->string('dosage')->nullable()->default(null);
            $table->string('dosage_form')->nullable()->default(null);
            $table->string('sub_indication')->nullable()->default(null);
            $table->longText('indication')->nullable()->default(null);
            $table->double('price')->nullable()->default(null);
            $table->string('active_ingredient')->nullable()->default(null);
            $table->boolean('is_pushed')->default(1);
            $table->integer('created_by')->nullable()->default(null)->unsigned();
            $table->integer('updated_by')->nullable()->default(null)->unsigned();
            $table->timestamps();
        });
        Schema::table('medications', function (Blueprint $table) {
            $table->foreign('created_by')
                ->references('id')
                ->on('users')
                ->onUpdate('cascade')
                ->onDelete('set null');
        });
        Schema::table('medications', function (Blueprint $table) {
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
        Schema::dropIfExists('medications');
    }
}
