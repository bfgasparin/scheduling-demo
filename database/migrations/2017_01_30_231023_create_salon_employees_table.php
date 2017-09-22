<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSalonEmployeesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('salon_employees', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('salon_id');
            $table->string('name');
            $table->string('email', 72);
            $table->string('password', 60);
            $table->boolean('is_professional', true);
            $table->boolean('is_admin', false);
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('salon_id')
                  ->references('id')->on('salons')
                  ->onDelete('cascade');

            $table->unique(['email', 'salon_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('salon_employees');
    }
}
