<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSalonClientsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('salon_clients', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('salon_id');
            $table->string('name');
            $table->string('email', 72);
            $table->string('cellphone', 11);
            $table->unsignedInteger('user_id')->nullable()->comment(
                "The application user that represent this client. It\'s null if the client ".
                'is created by a salon worker'
            );
            $table->timestamps();

            $table->foreign('salon_id')
                  ->references('id')->on('salons')
                  ->onDelete('cascade');

            $table->foreign('user_id')
                  ->references('id')->on('users')
                  ->onDelete('cascade');

            $table->unique(['user_id', 'salon_id']);
            $table->unique(['email', 'salon_id']);
            $table->unique(['cellphone', 'salon_id']);

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('salon_clients');
    }
}
