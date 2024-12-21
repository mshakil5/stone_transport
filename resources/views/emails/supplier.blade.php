@component('mail::message')

# {{ $subject }}

{!! $body !!}

Thanks,  
{{ config('app.name') }}

@endcomponent