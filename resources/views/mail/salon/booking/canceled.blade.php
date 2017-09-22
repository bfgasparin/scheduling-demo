@component('mail::message')
# A booking was canceled

This email is a confirmation that the booking nº {{ $booking->id }} from the client
'{{ $booking->client->name }}' was canceled successfully.

@component('mail::button', ['url' => ''])
See the booking
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
