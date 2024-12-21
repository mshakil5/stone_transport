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
                        <h3 class="card-title" id="cardTitle">Transfer To Warehouse</h3>
                        <input type="hidden" value="{{ $warehouseCount }}" id="warehouseCount">
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

                            <!-- Right side: Transfer to Warehouse -->
                            <div class="col-md-7">
                                <h5>Transfer to Warehouse</h5>
                                <form action="{{ route('transferToWarehouse', $purchase->id) }}" method="POST" id="transferForm">
                                    @csrf
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Product</th>
                                                <th>Size</th>
                                                <th>Color</th>
                                                <th>Quantity</th>
                                                <th>Warehouse 
                                                    <span class="badge badge-success" style="cursor: pointer;" data-toggle="modal" data-target="#addWarehouseModal">Add New</span>
                                                </th>
                                                <th>Remove</th>
                                            </tr>
                                        </thead>
                                        <tbody id="selectedProducts">
                                        </tbody>
                                    </table>

                                    <button type="submit" class="btn btn-success" id="transferBtn" disabled>Transfer</button>
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

<!-- Add Warehouse Modal -->
<div class="modal fade" id="addWarehouseModal" tabindex="-1" role="dialog" aria-labelledby="addWarehouseModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addWarehouseModalLabel">Add New Warehouse</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="newWarehouseForm">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="warehouse_name">Warehouse Name*</label>
                                <input type="text" class="form-control" id="warehouse_name" name="name" placeholder="Enter warehouse name" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="location">Location*</label>
                                <input type="text" class="form-control" id="location" name="location" placeholder="Enter location" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="operator_name">Operator Name*</label>
                                <input type="text" class="form-control" id="operator_name" name="operator_name" placeholder="Enter operator name" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="operator_phone">Operator Phone*</label>
                                <input type="number" class="form-control" id="operator_phone" name="operator_phone" placeholder="Enter operator phone" required>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="saveBtn">Save</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')

<script>
    window.transferredQuantities = {};

    window.transferToRight = function(historyId, productName, availableQuantity, productSize, productColor) {
        const warehouseLimit = parseInt($('#warehouseCount').val()); 
        if (transferredQuantities[historyId] >= warehouseLimit) {
            swal({
                title: "Error",
                text: "This product has already been transferred to the maximum number of warehouses.",
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
                        <select name="warehouses[${historyId}][]" id="warehouse" class="form-control" required>
                            @foreach ($warehouses as $warehouse)
                                <option value="{{ $warehouse->id }}">{{ $warehouse->name }}</option>
                            @endforeach
                        </select>
                    </td>
                    <td>
                        <button type="button" class="btn btn-danger btn-sm" onclick="removeRow('${rowId}', ${historyId})">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                    </td>
                </tr>
            `;

        $('#selectedProducts').append(row);

        $('#transferBtn').prop('disabled', true);

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

        $('#transferBtn').prop('disabled', hasZeroQuantity);
    }

    $('#saveBtn').click(function(e) {
        e.preventDefault();

        var formData = $('#newWarehouseForm').serialize();

        $.ajax({
            url: '{{ route("warehouse.store") }}',
            method: 'POST',
            data: formData,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if(response.success) {
                    // Add new warehouse to existing select dropdowns
                    $('select[name^="warehouses"]').each(function() {
                        $(this).append(
                            $('<option>', {
                                value: response.warehouse.id,
                                text: response.warehouse.name
                            })
                        );
                    });

                    $('#addWarehouseModal').modal('hide');
                    $('#newWarehouseForm')[0].reset();

                    let warehouseCountInput = $('#warehouseCount');
                    let currentWarehouseCount = parseInt(warehouseCountInput.val());
                    currentWarehouseCount++;
                    warehouseCountInput.val(currentWarehouseCount);

                    swal({
                        text: "Warehouse created successfully",
                        icon: "success",
                        button: {
                            text: "OK",
                            className: "swal-button--confirm"
                        }
                    });
                } else {
                    alert('Error: ' + response.message);
                }
            },
            error: function(xhr) {
                console.error(xhr.responseText);
                swal({
                    text: "An error occurred",
                    icon: "error",
                    button: {
                        text: "OK",
                        className: "swal-button--confirm"
                    }
                });
            }
        });
    });

    // $('#transferBtn').click(function() {
    //     $('#loader').show();
    //     $(this).prop('disabled', true);
    // });
</script>

<script>
    $(document).ready(function() {
        $('#transferForm').on('submit', function() {
            // Show the loader
            $('#loader').show();
            // Disable the submit button
            $('#transferBtn').prop('disabled', true);
        });
    });
</script>

@endsection