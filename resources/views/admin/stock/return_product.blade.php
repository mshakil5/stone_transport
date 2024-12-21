@extends('admin.layouts.admin')

@section('content')

<section class="content pt-3" id="contentContainer">
    <div class="container-fluid">
        <div class="row justify-content-md-center">
            <div class="col-md-12">
                <div class="card card-secondary">
                    <div class="card-header">
                        <h3 class="card-title">Return Products</h3>
                    </div>
                    <div class="card-body">
                        <form id="returnProductForm">
                            @csrf
                            <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="supplier_name">Supplier Name</label>
                                    <input type="text" class="form-control" id="supplier_name" name="supplier_name" value="{{ $purchase->supplier->name }}" readonly>
                                </div>
                            </div>
                            <input type="hidden" id="supplier_id" name="supplier_id" value="{{ $purchase->supplier->id }}">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="transaction_type">Transaction Type</label>
                                        <input type="text" class="form-control" id="transaction_type" value="{{ $purchase->purchase_type }}" readonly>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="ref">Reference</label>
                                        <input type="text" class="form-control" id="ref" value="{{ $purchase->ref }}" readonly>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="remarks">Remarks</label>
                                        <textarea class="form-control" id="remarks" rows="1" readonly>{{ $purchase->remarks }}</textarea>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="date">Return Date <span class="text-danger">*</span></label>
                                        <input type="date" class="form-control" id="date" name="date">
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="reason">Return Reason <span class="text-danger">*</span></label>
                                        <textarea class="form-control" id="return_reason" name="return_reason" rows="1"></textarea>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <h4>Products</h4>
                                    <table class="table table-bordered" id="productsTable">
                                        <thead>
                                            <tr>
                                                <th>Product</th>
                                                <th>Purchase Quantity</th>
                                                <th>Available Return Quantity</th>
                                                <th>Return Quantity</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($purchase->purchaseHistory as $history)
                                            @php
                                                $totalReturnedQuantity = \App\Models\PurchaseReturn::where('purchase_history_id', $history->id)->sum('return_quantity');

                                                $availableReturnQuantity = $history->quantity - $totalReturnedQuantity;
                                                $availableReturnQuantity = max(0, $availableReturnQuantity);
                                            @endphp
                                            <tr data-history-id="{{ $history->id }}" data-product-id="{{ $history->product->id }}">
                                                <td>{{ $history->product->name }}</td>
                                                <td>{{ $history->quantity }}</td>
                                                <td>{{ $availableReturnQuantity }}</td>
                                                <td>
                                                    <input type="number" class="form-control return_quantity" data-max="{{ $availableReturnQuantity }}">
                                                </td>
                                                <td>
                                                    <button type="button" class="btn btn-sm btn-secondary add-to-return">Return</button>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>

                                <div class="col-md-6">
                                    <h4>Products to Return</h4>
                                    <table class="table table-bordered" id="returnTable">
                                        <thead>
                                            <tr>
                                                <th>Product</th>
                                                <th>Purchase Quantity</th>
                                                <th>Return Quantity</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <!-- Return products will be appended here -->
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <div class="form-group">
                                <button type="submit" class="btn btn-secondary">Submit Return</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

@endsection

@section('script')

<script>
    $(document).ready(function() {
        var addedProducts = new Set();

        $(document).on('click', '.add-to-return', function() {
            var row = $(this).closest('tr');
            var historyId = row.data('history-id');
            var productId = row.data('product-id');
            var productName = row.find('td:eq(0)').text();
            var purchaseQuantity = parseFloat(row.find('td:eq(2)').text());
            var returnQuantity = parseFloat(row.find('input.return_quantity').val());

            if (isNaN(returnQuantity) || returnQuantity <= 0 || returnQuantity > purchaseQuantity) {
                alert('Return quantity must be between 1 and ' + purchaseQuantity);
                return;
            }

            if (addedProducts.has(historyId)) {
                alert('This product has already been added to the return list.');
                return;
            }

            var returnRow = `<tr data-history-id="${historyId}" data-product-id="${productId}">
                                <td>${productName}</td>
                                <td>${purchaseQuantity}</td>
                                <td><input type="number" class="form-control return-quantity" name="return_quantities[]" value="${returnQuantity}" readonly></td>
                                <td><button type="button" class="btn btn-sm btn-danger remove-from-return">Remove</button></td>
                            </tr>`;

            $('#returnTable tbody').append(returnRow);
            row.find('input.return_quantity').val('');
            $(this).hide();
            addedProducts.add(historyId);
        });

        $(document).on('click', '.remove-from-return', function() {
            var row = $(this).closest('tr');
            var historyId = row.data('history-id');
            $(`#productsTable tr[data-history-id="${historyId}"]`).find('.add-to-return').show(); 
            row.remove();
            addedProducts.delete(historyId);
        });

        $('#returnProductForm').submit(function(e) {
            e.preventDefault();

            var formData = {
                date: $('#date').val(),
                reason: $('#return_reason').val(),
                supplierId: $('#supplier_id').val(),
                products: []
            };

            $('#returnTable tbody tr').each(function() {
                var historyId = $(this).data('history-id');
                var productId = $(this).data('product-id');
                var returnQuantity = parseFloat($(this).find('input.return-quantity').val());

                formData.products.push({
                    purchase_history_id: historyId,
                    product_id: productId,
                    return_quantity: returnQuantity
                });
            });

            // console.log(formData);

            $.ajax({
                url: '/admin/submit-return',
                method: 'POST',
                data: formData,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    swal({
                        text: "Returned successfully",
                        icon: "success",
                        button: {
                            text: "OK",
                            className: "swal-button--confirm"
                        }
                    }).then(() => {
                        location.reload();
                    });
                },
                error: function(xhr) {
                    if (xhr.status === 422) {
                        let errors = xhr.responseJSON.errors;
                        let errorMessage = '';
                        $.each(errors, function(key, value) {
                            errorMessage += value[0] + '\n';
                        });

                        swal({
                            title: "Validation Error",
                            text: errorMessage,
                            icon: "error",
                            button: {
                                text: "OK",
                                className: "swal-button--confirm"
                            }
                        });
                    }
                }
            });
        });
    });
</script>

@endsection
