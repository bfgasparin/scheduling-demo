<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProfessionalWorkingJorneysTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('professional_working_jorneys', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('professional_id');
            $table->time('entry');
            $table->time('exit');
            $table->time('lunch');
            $table->string('days_of_week');
            $table->integer('calendar_interval')->comment('The interval bookings can be assigned to the professional in the calendar of this working Jorney');
            $table->timestamps();

            $table->foreign('professional_id')
                  ->references('id')->on('salon_employees')
                  ->onDelete('cascade');

            $table->unique('professional_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('professional_working_jorneys');
    }
}
