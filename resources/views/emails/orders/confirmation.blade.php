@component('mail::message')
# Order Confirmation

Dear {{ $order->user_id ? $order->user->name : $order->name }},

Thank you for your order. Below are the details of your purchase:

- **Invoice**#: {{ $order->invoice }}
- **Purchase Date**: {{ \Carbon\Carbon::parse($order->purchase_date)->format('F d, Y') }}
@if($order->order_type === 0)
- **Payment Method**: {{ $order->payment_method === 'CashOnDelivery' ? 'Cash On Delivery' : ucfirst($order->payment_method) }}
@endif
- **Total Amount**: {{ $order->net_amount }}

@component('mail::button', ['url' => $pdfUrl])
Download PDF Invoice
@endcomponent

Thanks for choosing us!

Regards,<br>
{{ config('app.name') }}
@endcomponent