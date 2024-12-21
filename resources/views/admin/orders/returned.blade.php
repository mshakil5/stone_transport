@extends('admin.layouts.admin')

@section('content')
<section class="content pt-3" id="contentContainer">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">

                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                    </div>
                @endif

                <div class="card card-secondary">
                    <div class="card-header">
                        <h3 class="card-title">Returned Orders</h3>
                    </div>
                    <div class="card-body">
                        <table id="example1" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Name/Email/Phone</th>
                                    <th>Total</th>
                                    <th>Returned Items</th>
                                    <th>Stock / System Loss</th>
                                    <th>Details</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($orders as $order)
                                    <tr>
                                    <td>
                                        {{ optional($order->user)->name ?? $order->name }} {{ optional($order->user)->surname ?? '' }} <br> {{ optional($order->user)->email ?? $order->email }} <br> {{ optional($order->user)->phone ?? $order->phone }}
                                    </td>
                                        <td>{{ number_format($order->net_amount, 2) }}</td>
                                        <td>
                                            @foreach ($order->orderReturns as $return)
                                                <p>
                                                    <strong>Product:</strong> {{ $return->product->name }}<br>
                                                    <strong>Quantity:</strong> {{ $return->quantity }}<br>
                                                    <strong>Reason:</strong> {{ $return->reason }}<br>
                                                    <strong>Returned By:</strong> {{ optional($return->returnedBy)->name }}
                                                </p>
                                            @endforeach
                                        </td>
                                        <td>      
                                            @foreach ($order->orderReturns as $return)
                                                <p>
                                                    <strong>Product:</strong> {{ $return->product->name }}<br>
                                                    <strong>Stock:</strong> {{ $return->return_stock }}<br>
                                                    <strong>System Loss:</strong> {{ $return->system_lose }}<br>
                                                </p>
                                            @endforeach
                                        </td>
                                        <td>
                                            <a href="{{ route('admin.orders.details', ['orderId' => $order->id]) }}" class="btn btn-info btn-round btn-shadow">
                                                <i class="fas fa-info-circle"></i> Details
                                            </a>
                                        </td>
                                        <td>
                                            <button class="btn btn-success btn-stock" data-order-id="{{ $order->id }}" data-returned-items='@json($order->orderReturns)' @if($order->orderReturns->sum('new_quantity') <= 0) disabled @endif>Send to Stock</button>
                                                <hr>
                                            <button class="btn btn-danger btn-system-loss" data-order-id="{{ $order->id }}" data-returned-items='@json($order->orderReturns)' @if($order->orderReturns->sum('new_quantity') <= 0) disabled @endif>System Loss</button>
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

<!-- Stock Modal -->
<div class="modal fade" id="stockModal" tabindex="-1" aria-labelledby="stockModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="stockForm" method="POST" action="{{ route('send.to.stock') }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="stockModalLabel">Send to Stock</h5>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="order_id" id="stockOrderId">
                    <div class="mb-3">
                        <label for="product" class="form-label">Product</label>
                        <select class="form-control" id="stockProductSelect" name="product_id" required>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="quantity" class="form-label">Quantity</label>
                        <input type="number" class="form-control" id="stockQuantity" name="quantity" min="1" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary close-btn" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Send to Stock</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- System Loss Modal -->
<div class="modal fade" id="systemLossModal" tabindex="-1" aria-labelledby="systemLossModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="systemLossForm" method="POST" action="{{ route('send.to.systemlose') }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="systemLossModalLabel">System Loss</h5>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="order_id" id="systemLossOrderId">
                    <div class="mb-3">
                        <label for="product" class="form-label">Product</label>
                        <select class="form-control" id="systemLossProductSelect" name="product_id" required>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="quantity" class="form-label">Quantity</label>
                        <input type="number" class="form-control" id="systemLossQuantity" name="quantity" min="1" required>
                    </div>
                    <div class="mb-3">
                        <label for="reason" class="form-label">Reason</label>
                        <textarea class="form-control" id="systemLossReason" name="reason" rows="3" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary close-btn" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">System Loss</button>
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

        $('.btn-stock').on('click', function() {
            var orderId = $(this).data('order-id');
            var returnedItems = $(this).data('returned-items');

            $('#stockOrderId').val(orderId);
            populateProductSelect('#stockProductSelect', returnedItems);
            $('#stockModal').modal('show');
        });

        $('.btn-system-loss').on('click', function() {
            var orderId = $(this).data('order-id');
            var returnedItems = $(this).data('returned-items');

            $('#systemLossOrderId').val(orderId);
            populateProductSelect('#systemLossProductSelect', returnedItems);
            $('#systemLossModal').modal('show');
        });

        function populateProductSelect(selectId, items) {
            var $select = $(selectId);
            $select.empty();
            items.forEach(function(item) {
                if (item.new_quantity > 0) {
                    $select.append('<option value="' + item.product.id + '" data-max-quantity="' + item.new_quantity + '">' + item.product.name + '</option>');
                }
            });

            $select.on('change', function() {
                var maxQuantity = $(this).find('option:selected').data('max-quantity');
                $(this).closest('.modal').find('input[type="number"]').attr('max', maxQuantity);
            }).trigger('change');
        }
    });
</script>

<script>
    $(document).ready(function() {
        $('.close-btn').on('click', function() {
            if ($('#stockModal').is(':visible')) {
                $('#stockModal').modal('hide');
            }
            if ($('#systemLossModal').is(':visible')) {
                $('#systemLossModal').modal('hide');
            }
        });
    });
</script>
@endsection