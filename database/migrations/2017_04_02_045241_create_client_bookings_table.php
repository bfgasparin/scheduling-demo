<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateClientBookingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('client_bookings', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('client_id');
            $table->unsignedInteger('professional_id');
            $table->unsignedInteger('service_id');
            $table->date('date');
            $table->decimal('service_price', 5, 2)->comment('The value of the service at the time of the booking. Need in avoid lost value in case of service changes');
            $table->time('start')->comment('The time booking was scheduled');
            $table->timestamp('canceled_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('client_id')
                  ->references('id')->on('salon_clients')
                  ->onDelete('cascade');

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
        Schema::dropIfExists('client_bookings');
    }
}
