@extends('admin.layouts.admin')

@section('content')



<section class="content pt-3" id="addThisFormContainer">

    <div class="container-fluid">

    @if(session()->has('success'))
        <div class="alert alert-success">
            {{ session()->get('success') }}
        </div>
    @endif

        <div class="row justify-content-md-center">
            <div class="col-md-12">
                <div class="card card-secondary">
                    <div class="card-header">
                        <h3 class="card-title" id="cardTitle">Missing Product</h3>
                        <input type="hidden" value="{{ $purchaseCount }}" id="purchaseCount">
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <!-- Left side: Product list -->
                            <div class="col-md-5">
                                <h5>Products</h5>
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Code</th>
                                            <th>Product</th>
                                            <th>Quantity</th>
                                            <th>Size</th>
                                            <th>Color</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody id="productList">
                                        @foreach ($purchase->purchaseHistory as $key => $history)
                                            @if ($history->remaining_product_quantity > 0)
                                            <tr id="product-{{ $history->id }}">
                                                <td>{{ $history->product->product_code }}</td>
                                                <td>{{ $history->product->name }}</td>
                                                <td>{{ $history->remaining_product_quantity }}</td>
                                                <td>{{ $history->product_size }}</td>
                                                <td>{{ $history->product_color }}</td>
                                                <td>
                                                <button class="btn btn-sm btn-success transfer-btn" 
                                                        onclick="transferToRight({{ $history->id }}, '{{ $history->product->name }}', {{ $history->remaining_product_quantity }}, '{{ $history->product_size }}', '{{ $history->product_color }}')">
                                                    <i class="fas fa-arrow-right"></i>
                                                </button>
                                                </td>
                                            </tr>
                                            @endif
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <!-- Right side: Missing Product -->
                            <div class="col-md-7">
                                <h5>Missing Product</h5>
                                <form action="{{ route('missingPurchaseProduct', $purchase->id) }}" method="POST" id="transferForm">
                                    @csrf
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Product</th>
                                                <th>Size</th>
                                                <th>Color</th>
                                                <th>Quantity</th>
                                                <th>Remove</th>
                                            </tr>
                                        </thead>
                                        <tbody id="selectedProducts">
                                        </tbody>
                                    </table>

                                    <button type="submit" class="btn btn-success" id="saveBtn" disabled>Save</button>
                                    <div id="loader" style="display: none;">
                                        <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                        Loading...
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>


@endsection

@section('script')

<script>
    window.transferredQuantities = {};

    window.transferToRight = function(historyId, productName, availableQuantity, productSize, productColor) {
        
        if (transferredQuantities[historyId] >= 1) {
            swal({
                title: "Error",
                text: "This product has already added.",
                icon: "error",
                button: "OK",
            });
            return;
        }

        const rowId = `transfer-product-${historyId}-${Date.now()}`;

        const row = `
                <tr id="${rowId}">
                    <td>${productName}</td>
                    <td>${productSize}</td>
                    <td>${productColor}</td>
                    <td>
                        <input type="number" name="quantities[${historyId}][]" value="1" min="1" max="${availableQuantity}" class="form-control product-quantity" data-max="${availableQuantity}" required>
                        <input type="hidden" name="sizes[${historyId}][]" value="${productSize}">
                        <input type="hidden" name="colors[${historyId}][]" value="${productColor}">
                    </td>
                    <td>
                        <button type="button" class="btn btn-danger btn-sm" onclick="removeRow('${rowId}', ${historyId})">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                    </td>
                </tr>
            `;

        $('#selectedProducts').append(row);

        $('#saveBtn').prop('disabled', true);

        if (!transferredQuantities[historyId]) {
            transferredQuantities[historyId] = 0;
        }

        transferredQuantities[historyId]++;
        checkQuantities();
    };

    function updateTotalQuantity(historyId, maxAvailableQuantity) {
        let totalTransferred = 0;

        $(`input[name="quantities[${historyId}][]"]`).each(function() {
            totalTransferred += parseInt($(this).val()) || 0;
        });

        if (totalTransferred > maxAvailableQuantity) {
            swal({
                title: "Error",
                text: `Total quantity for this product cannot exceed ${maxAvailableQuantity}.`,
                icon: "error",
                button: "OK",
            });

            $(`input[name="quantities[${historyId}][]"]`).each(function() {
                $(this).val(0);
            });
        }

        checkQuantities();
    }

    $(document).on('change', '.product-quantity', function() {
        const historyId = $(this).attr('name').match(/\d+/)[0];
        const maxAvailableQuantity = $(this).data('max');
        updateTotalQuantity(historyId, maxAvailableQuantity);
    });

    window.removeRow = function(rowId, historyId) {
        $(`#${rowId}`).remove();
        transferredQuantities[historyId]--;
        checkQuantities();
    };

    function checkQuantities() {
        let hasZeroQuantity = false;

        $('.product-quantity').each(function() {
            if ($(this).val() === "0") {
                hasZeroQuantity = true;
            }
        });

        $('#saveBtn').prop('disabled', hasZeroQuantity);
    }


</script>

<script>
    $(document).ready(function() {
        $('#transferForm').on('submit', function() {
            // Show the loader
            $('#loader').show();
            // Disable the submit button
            $('#saveBtn').prop('disabled', true);
        });
    });
</script>

@endsection