<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSmsMessagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sms_messages', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->morphs('recipient');
            $table->string('channel')->comment('The name of the notification channel used to sent the sms messages');
            $table->string('channel_id')->comment('The id of the message from the notification channel');
            $table->json('channel_response')->comment('The response of notification channel who sent the sms');
            $table->json('delivery_receipt')->nullable()->comment('The Delivery Receipt sent by the carrier explaning the status of the message');
            $table->timestamp('delivered_at')->nullable()->comment('The date the recipient received the message');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sms_messages');
    }
}
