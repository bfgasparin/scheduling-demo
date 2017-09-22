<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSalonConfigBookingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('salon_config_bookings', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('salon_id');
            $table->integer('cancel_tolerance_for_client_user')->comment('Time in minutes the salon allows a client user to cancel a booking before that booking starts');
            $table->integer('create_tolerance_for_client_user')->comment('Time in minutes the salon allows a client user to create a booking before current time');
            $table->integer('calendar_interval')->comment('Interval in minutes to split the salon booking calendar');
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
        Schema::dropIfExists('salon_config_bookings');
    }
}
