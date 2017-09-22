@component('mail::message')
# Booking Canceled

This email is just to confirm that your booking nº {{ $booking->id }} was canceled successfully.

@component('mail::button', ['url' => ''])
See you booking
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
