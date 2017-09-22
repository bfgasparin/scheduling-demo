@component('mail::message')
# Booking Cancel Failed

Your booking cancel for booking nº {{ $booking->id }} failed.

Reason: (( $reason ))

@component('mail::button', ['url' => ''])
See you booking
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
