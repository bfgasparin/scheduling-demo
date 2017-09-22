@component('mail::message')
# Booking Creation Failed

The booking for {{ $clientName }} with {{ $service->name ]} and profissional {{ $professional->name }} on date {{ $data }} could
not be created.

Reason: (( $reason ))

@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
