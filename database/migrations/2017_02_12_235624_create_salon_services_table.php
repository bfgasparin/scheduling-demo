<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSalonServicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('salon_services', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->text('description');
            $table->unsignedInteger('salon_id');
            $table->decimal('price', 5, 2);
            $table->integer('duration');
            $table->softDeletes();
            $table->decimal('cost', 5, 2);
            $table->enum('client_visibility', ['always', 'never'])->default('always');
            $table->timestamps();

            $table->foreign('salon_id')
                  ->references('id')->on('salons')
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
        Schema::dropIfExists('salon_services');
    }
}
