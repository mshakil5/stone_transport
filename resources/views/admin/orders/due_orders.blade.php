@extends('admin.layouts.admin')

@section('content')
<section class="content pt-3" id="contentContainer">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card card-secondary">
                    <div class="card-header">
                        <h3 class="card-title">All Data</h3>
                    </div>
                    <div class="card-body">
                        <table id="example1" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Name/Email/Phone</th>
                                    <th>Subtotal</th>
                                    <th>Vat</th>
                                    <th>Discount</th>
                                    <th>Total</th> 
                                    <th>Due Status</th> 
                                    <th>Due Amount</th> 
                                    <th>Received Amount</th> 
                                    <th>Payment Type</th>
                                    <th>Details</th>     
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($orders as $order)
                                <tr>
                                    <td>{{ \Carbon\Carbon::parse($order->purchase_date)->format('d-m-Y') }}</td>
                                    <td>
                                        {{ optional($order->user)->name ?? $order->name }} {{ optional($order->user)->surname ?? '' }} <br> {{ optional($order->user)->email ?? $order->email }} <br> {{ optional($order->user)->phone ?? $order->phone }}
                                    </td>
                                    <td>{{ number_format($order->subtotal_amount, 2) }}</td>
                                    <td>{{ number_format($order->vat_amount, 2) }}</td>
                                    <td>{{ number_format($order->discount_amount, 2) }}</td>
                                    <td>{{ number_format($order->net_amount, 2) }}</td>
                                    <td>
                                        @if ($order->due_status == 1)
                                            <span class="badge bg-success">Full Received</span>
                                        @else
                                            <span class="badge bg-danger">Due Pending</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="align-items-center">

                                        @php
                                            $netAmount = $order->due_amount - $order->received_amount;
                                        @endphp

                                        @if ($netAmount > 0)
                                            <span class="btn btn-sm btn-danger">£ {{ number_format($netAmount, 2) }}</span>
                                            <button class="btn btn-sm btn-warning pay-btn" data-id="{{ $order->id }}" data-customer-id="{{ $order->user_id }}" data-net-amount="{{ $netAmount }}">Receive</button>
                                        @endif
                                        </div>
                                    </td>
                                    <td>{{ number_format($order->received_amount, 2) }}</td>
                                    <td>
                                        @if($order->payment_method === 'cashOnDelivery')
                                            Cash On Delivery
                                        @elseif($order->payment_method === 'paypal')
                                            PayPal
                                        @elseif($order->payment_method === 'stripe')
                                            Stripe
                                        @else
                                            {{ $order->payment_method }}
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('in-house-sell.generate-pdf', ['encoded_order_id' => base64_encode($order->id)]) }}" class="btn btn-success btn-round btn-shadow" target="_blank">
                                            <i class="fas fa-receipt"></i> Invoice
                                        </a>
                                        <a href="{{ route('admin.orders.details', ['orderId' => $order->id]) }}" class="btn btn-info btn-round btn-shadow">
                                            <i class="fas fa-info-circle"></i> Details
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<div class="modal fade" id="payModal" tabindex="-1" role="dialog" aria-labelledby="payModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="payModalLabel">WholeSaler Payment Form</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="payForm">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="paymentAmount">Payment Amount</label>
                        <input type="number" class="form-control" id="paymentAmount" name="paymentAmount" placeholder="Enter payment amount">
                    </div>
                    
                    <div class="form-group">
                        <label for="document">Document</label>
                        <input type="file" class="form-control-file" id="document" name="document">
                    </div>

                    <div class="form-group">
                        <label for="payment_type">Payment Type</label>
                        <select name="payment_type" id="payment_type" class="form-control" >
                            <option value="Cash">Cash</option>
                            <option value="Bank">Bank</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="paymentNote">Payment Note</label>
                        <textarea class="form-control" id="paymentNote" name="paymentNote" rows="3" placeholder="Enter payment note"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning">Receive</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('script')
<script>
    $(function () {
        $("#example1").DataTable({
            "responsive": true,
            "lengthChange": false,
            "autoWidth": false,
            "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"]
        }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
    });
</script>

<script>
    $(document).ready(function () {
        $(document).on('click', '.pay-btn', function () {
            const orderId = $(this).data('id');
            const customerId = $(this).data('customer-id');
            const netAmount = $(this).data('net-amount');

            $('#payForm').data('order-id', orderId);
            $('#payForm').data('customer-id', customerId);
            $('#paymentAmount').data('max-amount', netAmount);

            $('#payModal').modal('show');
        });

        $('#paymentAmount').on('input', function() {
            const maxAmount = $(this).data('max-amount');
            const inputAmount = parseFloat($(this).val());

            if (inputAmount > maxAmount) {
                alert('Payment amount cannot exceed £' + maxAmount.toFixed(2));
                $(this).val('');
            }
        });

        $('#payForm').on('submit', function (e) {
            e.preventDefault();

            const formData = new FormData(this);
            formData.append('order_id', $(this).data('order-id'));
            formData.append('customer_id', $(this).data('customer-id'));

            if (!formData.get('paymentAmount')) {
                alert('Please enter an amount.');
                return;
            }

            const loader = $('<div class="loader">Processing...</div>').appendTo('body');
            // for (const [key, value] of formData.entries()) {
            //     console.log(key, value);
            // }

            $.ajax({
                url: '{{ URL::to('/admin/customer-order-pay') }}',
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (response) {
                    $('#payModal').modal('hide');
                    $('#payForm')[0].reset();

                    swal({
                        text: "Payment store successfully",
                        icon: "success",
                        button: {
                            text: "OK",
                            className: "swal-button--confirm"
                        }
                    }).then(() => {
                        location.reload();
                    });
                },
                error: function (xhr, status, error) {
                    console.error(xhr.responseText);
                    alert('An error occurred: ' + xhr.responseText);
                },
                complete: function () {
                    loader.remove();
                }
            });
        });
    });
</script>

@endsection