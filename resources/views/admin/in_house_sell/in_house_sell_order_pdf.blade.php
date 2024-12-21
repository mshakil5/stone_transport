<!DOCTYPE html>
<html lang="en">
<head>
@php
    $company = \App\Models\CompanyDetails::first();
@endphp 

    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>{{ $company->company_name }} - Invoice</title>
    <style>
        .invoice-box {
            max-width: 800px;
            margin: auto;
            padding: 30px;
            border: 1px solid #eee;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.15);
            font-size: 14px;
            line-height: 24px;
            font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif;
            color: #555;
        }

        .invoice-box table {
            width: 100%;
            line-height: inherit;
            text-align: left;
        }

        .invoice-box table td {
            padding: 5px;
            vertical-align: top;
        }

        .invoice-box table tr td:nth-child(2) {
            text-align: right;
        }

        .invoice-box table tr.top table td {
            padding-bottom: 20px;
        }

        .invoice-box table tr.top table td.title {
            font-size: 45px;
            line-height: 45px;
            color: #333;
        }

        .invoice-box table tr.information table td {
            padding-bottom: 40px;
        }

        .invoice-box table tr.heading td {
            background: #eee;
            border-bottom: 1px solid #ddd;
            font-weight: bold;
        }

        .invoice-box table tr.details td {
            padding-bottom: 20px;
        }

        .invoice-box table tr.item td {
            border-bottom: 1px solid #eee;
        }

        .invoice-box table tr.item.last td {
            border-bottom: none;
        }

        .invoice-box table tr.total td:nth-child(2) {
            border-top: 2px solid #eee;
            font-weight: bold;
        }

        .sub-total td:first-child,
        .total td:first-child {
            width: 80%;
            padding-left: 70%;
            text-align: left;
        }

        .right-align {
            text-align: right;
            width: 50%;
        }

        @media only screen and (max-width: 600px) {
            .invoice-box table tr.top table td {
                width: 100%;
                display: block;
                text-align: center;
            }

            .invoice-box table tr.information table td {
                width: 100%;
                display: block;
                text-align: center;
            }
        }

        /** RTL **/
        .invoice-box.rtl {
            direction: rtl;
            font-family: Tahoma, 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif;
        }

        .invoice-box.rtl table {
            text-align: right;
        }

        .invoice-box.rtl table tr td:nth-child(2) {
            text-align: left;
        }

        .line {
            border-top: 1px solid #eee;
        }

    </style>
</head>

@php
    $company = \App\Models\CompanyDetails::first();
    use Carbon\Carbon;
@endphp 

<body>
    <div class="invoice-box">
        <table cellpadding="0" cellspacing="0">
            <tr class="top">
                <td colspan="2">
                    <table>
                        <tr>
                            <td class="title">
                                <img src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('images/company/'.$company->company_logo))) }}" style="width: 100%; max-width: 150px" />
                            </td>
                            <td>
                                Invoice #: {{ $order->invoice }}<br />
                                Purchase Date: {{ \Carbon\Carbon::parse($order->purchase_date)->format('F d, Y') }}<br />
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>

            <tr class="information">
                <td colspan="2">
                    <table>
                        <tr>
                            <td>
                                {{ $company->address1 }} <br />
                                {{ $company->address2 }} <br />
                                {{ $company->phone1 }}
                            </td>
                            <td>
                                {{ $order->user->name }}<br />
                                {{ $order->user->email }}<br />
                                {{ $order->user->phone }}
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>

            <tr class="heading">
                <td>Payment Method</td>
                <td>Amount</td>
            </tr>

            <tr class="details">
                <td>{{ $order->payment_method }}</td>
                <td>{{ $currency }} {{ $order->net_amount }}</td>
            </tr>

            <tr class="heading">
                <td>Item</td>
                <td>Price</td>
            </tr>

            @foreach ($order->orderDetails as $detail)
            <tr class="item {{ $loop->last ? 'last' : '' }}">
                <td>{{ $detail->product->name }} ({{ $detail->quantity }} x {{ $currency }} {{ $detail->price_per_unit }})</td>
                <td>{{ $currency }} {{ $detail->total_price }}</td>
            </tr>
            @endforeach

            <tr>
                <td colspan="2">
                    <div class="line"></div>
                </td>
            </tr>

            <tr class="sub-total">
                <td class="text-left fixed-width">Vat:</td>
                <td class="right-align">{{ $currency }} {{ number_format($order->vat_amount ?? 0.00, 2) }}</td>
            </tr>

            <tr class="sub-total">
                <td class="text-left fixed-width">Shipping:</td>
                <td class="right-align">{{ $currency }} {{ number_format($order->shipping_amount ?? 0.00, 2) }}</td>
            </tr>

            <tr class="sub-total">
                <td class="text-left fixed-width">Discount Amount:</td>
                <td class="right-align">{{ $currency }} {{ number_format($order->discount_amount ?? 0.00, 2) }}</td>
            </tr>

            <tr class="sub-total">
                <td class="text-left fixed-width">Sub Total:</td>
                <td class="right-align">{{ $currency }} {{ number_format($order->subtotal_amount ?? 0.00, 2) }}</td>
            </tr>

            <tr class="total">
                <td class="text-left fixed-width" style="font-weight: bold;">Total</td>
                <td class="right-align">{{ $currency }} {{ number_format($order->net_amount ?? 0.00, 2) }}</td>
            </tr>

        </table>
    </div>
</body>
</html>