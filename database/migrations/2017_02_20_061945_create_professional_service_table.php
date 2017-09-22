<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProfessionalServiceTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('professional_service', function (Blueprint $table) {
            $table->unsignedInteger('professional_id');
            $table->unsignedInteger('service_id');
            $table->integer('duration')->nullable();
            $table->timestamps();

            $table->foreign('professional_id')
                  ->references('id')->on('salon_employees')
                  ->onDelete('cascade');
            $table->foreign('service_id')
                  ->references('id')->on('salon_services')
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
        Schema::dropIfExists('professional_service');
    }
}
