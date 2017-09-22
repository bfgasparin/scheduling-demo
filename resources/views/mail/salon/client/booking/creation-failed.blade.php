@component('mail::message')
# Booking Creation Failed

Your booking on date {{ $date }} for {{ $service->name ]} on salon {{ $salon->name }} could not be created.

Reason: (( $reason ))

@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
