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
                                    <th>Payment Type</th> 
                                    <th>Type</th>  
                                    <th>Status</th>                                       
                                     <th>Warehouse</th>     
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
                                    <td>{{ $order->order_type == 0 ? 'Frontend' : 'In-house Sale' }}</td>
                                    <td>
                                        <select class="form-control order-status" data-order-id="{{ $order->id }}"
                                            {{ empty($order->warehouse_id) ? 'disabled' : '' }}>
                                            {{-- <option value="1" {{ $order->status == 1 ? 'selected' : '' }}>Pending</option> --}}
                                            <option value="2" {{ $order->status == 2 ? 'selected' : '' }}>Processing</option>
                                            {{-- <option value="3" {{ $order->status == 3 ? 'selected' : '' }}>Packed</option> --}}
                                            {{-- <option value="4" {{ $order->status == 4 ? 'selected' : '' }}>Shipped</option> --}}
                                            <option value="5" {{ $order->status == 5 ? 'selected' : '' }}>Delivered</option>
                                            {{-- <option value="6" {{ $order->status == 6 ? 'selected' : '' }}>Returned</option> --}}
                                            <option value="7" {{ $order->status == 7 ? 'selected' : '' }}>Cancelled</option>
                                        </select>
                                    </td>
                                    <td>
                                        @if (empty($order->warehouse_id))
                                            <select class="form-control select-warehouse" data-order-id="{{ $order->id }}">
                                                <option value="">Select Warehouse</option>
                                                @foreach ($warehouses as $warehouse)
                                                    <option value="{{ $warehouse->id }}">
                                                        {{ $warehouse->name }} - {{ $warehouse->location }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        @else
                                            <span>{{ $order->warehouse->name }}</span>
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
<div id='loading' style='display:none ;'>
    <img src="{{ asset('loader.gif') }}" id="loading-image" alt="Loading..." />
</div>

<style>
    #loading {
    position: fixed;
    display: flex;
    justify-content: center;
    align-items: center;
    width: 100%;
    height: 100%;
    top: 0;
    left: 0;
    opacity: 0.7;
    background-color: #fff;
    z-index: 99;
}

    #loading-image {
        z-index: 100;
    }
</style>

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

        $('.order-status').change(function() {
            const orderId = $(this).data('order-id');
            const status = $(this).val();

            $.ajax({
                url: '/admin/orders/update-status',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    order_id: orderId,
                    status: status
                },
                beforeSend: function() {
                    $('#loading').show();
                },
                success: function(response) {
                    swal({
                        text: "Status Changed",
                        icon: "success",
                        button: {
                            text: "OK",
                            className: "swal-button--confirm"
                        }
                    }).then(() => {
                        location.reload();
                    });
                },
                error: function(xhr, status, error) {
                    console.log(xhr.responseText);
                },
                complete: function() {
                    $('#loading').hide();
                }
            });
        });
    });
</script>

<script>
    $(document).ready(function() {
        $('.select-delivery-man').change(function() {
            const orderId = $(this).data('order-id');
            const deliveryManId = $(this).val();
            // console.log(orderId, deliveryManId);

            $.ajax({
                url: '/admin/orders/update-delivery-man', 
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    order_id: orderId,
                    delivery_man_id: deliveryManId
                },
                success: function(response) {
                    // console.log(response);
                    swal({
                        text: "Delivery man assigned",
                        icon: "success",
                        button: {
                            text: "OK",
                            className: "swal-button--confirm"
                        }
                    }).then(() => {
                        location.reload();
                    });
                },
                error: function(xhr, status, error) {
                    console.log(xhr.responseText);
                }
            });
        });

        $('.select-warehouse').change(function() {
            const orderId = $(this).data('order-id');
            const warehouseId = $(this).val();

            if (warehouseId) {
                swal({
                    title: "Are you sure?",
                    text: "Do you want to assign this warehouse?",
                    icon: "warning",
                    buttons: true,
                    dangerMode: true,
                }).then((willAssign) => {
                    if (willAssign) {
                        $.ajax({
                            url: '{{ route('assign.warehouse') }}',
                            type: 'POST',
                            data: {
                                order_id: orderId,
                                warehouse_id: warehouseId,
                                _token: '{{ csrf_token() }}'
                            },
                            success: function(response) {
                                swal({
                                    icon: 'success',
                                    title: 'Success',
                                    text: 'Warehouse assigned successfully!',
                                }).then(() => {
                                    location.reload();
                                });
                            },
                            error: function(xhr) {
                                console.log(xhr.responseText);
                                swal({
                                    icon: 'error',
                                    title: 'Error',
                                    text: 'There was an error assigning the warehouse. Please try again.',
                                });
                            }
                        });
                    } else {
                        $(this).val('');
                    }
                });
            } else {
                swal({
                    icon: 'warning',
                    title: 'Warning',
                    text: 'Please select a warehouse.',
                });
            }
        });
    });
</script>

@endsection