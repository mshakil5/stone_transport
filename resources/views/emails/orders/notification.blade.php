@component('mail::message')
# New Message Regarding Invoice #{{ $order->invoice }}

Hello Admin,

You have received a new message regarding **Invoice #{{ $order->invoice }}**.

### Message:
{!! $message !!}

Thank you,  
{{ config('app.name') }}
@endcomponent