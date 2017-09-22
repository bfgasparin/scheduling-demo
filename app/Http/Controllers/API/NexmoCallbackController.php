<?php

namespace App\Http\Controllers\API;

use App\SmsMessage;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

/**
 * Actions for Nexmo callback uris
 * @see https://docs.nexmo.com/messaging/setup-callbacks
 */
class NexmoCallbackController extends Controller
{
    /**
     * Handle a POST Nexmo callback
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        SmsMessage::nexmoMessage($request->input('messageId'))
            ->firstOrFail()
            ->markAsDeliveredFromNexmoDLR($request->all());
    }
}
