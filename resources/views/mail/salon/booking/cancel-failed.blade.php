@component('mail::message')
# A booking cancel failed

This email is a notification that the booking nº {{ $booking->id }} cancel from the client
'{{ $booking->client->name }}' failed

Reason: (( $reason ))

@component('mail::button', ['url' => ''])
See the booking
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
