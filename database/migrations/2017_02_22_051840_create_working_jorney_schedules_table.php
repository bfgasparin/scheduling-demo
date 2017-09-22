<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWorkingJorneySchedulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('working_jorney_schedules', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('working_jorney_id');
            $table->date('date');
            $table->time('entry');
            $table->time('exit');
            $table->timestamps();

            $table->foreign('working_jorney_id')
                  ->references('id')->on('professional_working_jorneys')
                  ->onDelete('cascade');

            $table->unique(['date', 'working_jorney_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('working_jorney_schedules');
    }
}
