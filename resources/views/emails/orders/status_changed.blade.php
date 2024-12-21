@component('mail::message')
# Order Status Update

The status of your order with invoice number **{{ $order->invoice }}** has been updated to: 
<strong>
    @if($order->status == 1)
        Pending
    @elseif($order->status == 2)
        Processing
    @elseif($order->status == 3)
        Packed
    @elseif($order->status == 4)
        Shipped
    @elseif($order->status == 5)
        Delivered
    @elseif($order->status == 6)
        Returned
    @elseif($order->status == 7)
        Cancelled
    @else
        Unknown
    @endif
</strong>

Thank you for choosing us!

Regards,  
{{ config('app.name') }}
@endcomponent