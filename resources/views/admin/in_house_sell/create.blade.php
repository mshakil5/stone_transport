@extends('admin.layouts.admin')

@section('content')

<section class="content pt-3" id="addThisFormContainer">
    <div class="container-fluid">
        <div class="row justify-content-md-center">
            <div class="col-md-12">
                <div class="card card-secondary">
                    <div class="card-body">
                        <div class="ermsg"></div>
                        <form id="createThisForm">
                            @csrf
                            <div class="row">

                                <div class="col-sm-2">
                                    <div class="form-group">
                                        <label for="purchase_date">Selling Date <span class="text-danger">*</span></label>
                                        <input type="date" class="form-control" id="purchase_date" name="purchase_date" value="{{ now()->format('Y-m-d') }}">
                                    </div>
                                </div>
                                
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label for="user_id">Customer/Party <span class="text-danger">*</span>
                                            <span class="badge bg-success" style="cursor: pointer;" data-toggle="modal" data-target="#newWholeSalerModal">
                                                <i class="fas fa-plus"></i> Add
                                            </span>
                                        </label>
                                        <select class="form-control" id="user_id" name="user_id">
                                            <option value="">Select...</option>
                                            @foreach($customers as $customer)
                                                <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label for="vehicle_number">Vehicle Number <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="vehicle_number" name="vehicle_number">
                                    </div>
                                </div>
                                
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label for="destination">Destination <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="destination" name="destination">
                                    </div>
                                </div>
                            
                                <div class="col-sm-5">
                                    <div class="form-group">
                                        <label for="warehouse_id">Warehouse <span class="text-danger">*</span></label>
                                        <select name="warehouse_id" id="warehouse_id" class="form-control">
                                            <option value="">Select</option>
                                            @foreach ($warehouses as $warehouse)
                                            <option value="{{$warehouse->id}}">{{$warehouse->name}}-{{$warehouse->location}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="col-sm-2 d-none">
                                    <div class="form-group">
                                        <label for="purchase_type">Transaction Type <span class="text-danger">*</span></label>
                                        <select class="form-control" id="payment_method" name="payment_method">
                                            <option value="Credit" selected>Credit</option>
                                            <option value="Cash">Cash</option>
                                            <option value="Bank">Bank</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-sm-2 d-none">
                                    <div class="form-group">
                                        <label for="ref">Ref</label>
                                        <input type="text" class="form-control" id="ref" name="ref" placeholder="Enter reference">
                                    </div>
                                </div>

                                <div class="col-sm-4 d-none">
                                    <div class="form-group">
                                        <label for="remarks">Remarks</label>
                                        <textarea class="form-control" id="remarks" name="remarks" rows="1" placeholder="Enter remarks"></textarea>
                                    </div>
                                </div>

                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="product_id">Choose Product <span class="text-danger">*</span></label>
                                        <select class="form-control" id="product_id" name="product_id">
                                            <option value="">Select...</option>
                                            @foreach($products as $product)
                                            @php
                                                $sellingPrice = $product->stockhistory()
                                                            ->where('available_qty', '>', 0)
                                                            ->orderBy('id', 'asc')
                                                            ->value('selling_price');
                                            @endphp         
                                                <option value="{{ $product->id }}" data-name="{{ $product->name }}" data-price="{{ $sellingPrice }}">{{ $product->name }} - {{ $product->product_code }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="col-sm-1">
                                    <label for="addProductBtn">Action</label>
                                    <div class="col-auto d-flex align-items-end">
                                        <button type="button" id="addProductBtn" class="btn btn-secondary">Add</button>
                                     </div>
                                </div>

                                <div class="col-sm-12 mt-3">
                                    <h2>Product List:</h2>
                                    <table class="table table-bordered" id="productTable">
                                        <thead>
                                            <tr>
                                                <th>Product</th>
                                                <th>Quantity</th>
                                                <th>Unit Price</th>
                                                <th>Total Price</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            
                                        </tbody>
                                    </table>
                                </div>

                                <div class="container mt-4 mb-5">
                                    <div class="row">
                                        <!-- Left side -->
                                        <div class="col-md-6 d-none">
                                            <div class="mb-2">
                                                <div class="d-flex align-items-center">
                                                    <span>Coupon:</span>
                                                    <input type="text" class="form-control ml-2" id="couponName">
                                                </div>
                                            </div>
                                            <div class="d-flex justify-content-center">
                                                <button id="applyCoupon" class="btn btn-secondary">Apply Coupon</button>
                                            </div>
                                        </div>

                                        <!-- Right side -->
                                        <div class="col-md-6 offset-md-6">
                                            <div class="row mb-3">
                                                <div class="col-sm-6 d-flex align-items-center justify-content-end">
                                                    <span>Item Total Amount:</span>
                                                </div>
                                                <div class="col-sm-6">
                                                    <input type="text" class="form-control" id="item_total_amount" readonly>
                                                </div>
                                            </div>
                                            <div class="row mb-3">
                                                <div class="col-sm-6 d-flex align-items-center justify-content-end">
                                                    <span>Vat Percent(%):</span>
                                                </div>
                                                <div class="col-sm-6">
                                                    <input type="number" step="0.01" class="form-control" id="vat_percent" name="vat_percent">
                                                </div>
                                            </div>
                                            <div class="row mb-3">
                                                <div class="col-sm-6 d-flex align-items-center justify-content-end">
                                                    <span>Vat Amount:</span>
                                                </div>
                                                <div class="col-sm-6">
                                                    <input type="number" step="0.01" class="form-control" id="vat" name="vat" readonly>
                                                </div>
                                            </div>
                                            <div class="row mb-3">
                                                <div class="col-sm-6 d-flex align-items-center justify-content-end">
                                                    <span>Discount Amount:</span>
                                                </div>
                                                <div class="col-sm-6">
                                                    <input type="number" step="0.01" class="form-control" id="discount" name="discount">
                                                </div>
                                            </div>
                                            <div class="row mb-3">
                                                <div class="col-sm-6 d-flex align-items-center justify-content-end">
                                                    <span>Net Amount:</span>
                                                </div>
                                                <div class="col-sm-6">
                                                    <input type="text" class="form-control" id="net_amount" readonly>
                                                </div>
                                            </div>
                                            
                                            <div class="row mb-3">
                                                <div class="col-sm-6 d-flex align-items-center justify-content-end">
                                                    <span>Cash Received:</span>
                                                </div>
                                                <div class="col-sm-6">
                                                    <input type="number" class="form-control" id="cash_payment" name="cash_payment">
                                                    <span class="errmsg text-danger"></span>
                                                </div>
                                            </div>

                                            
                                            <div class="row">
                                                <div class="col-sm-6 d-flex align-items-center justify-content-end">
                                                    <span>Bank Received:</span>
                                                </div>
                                                <div class="col-sm-6">
                                                    <input type="text" class="form-control" id="bank_payment" name="bank_payment">
                                                    <span class="errmsg text-danger"></span>
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                </div>

                            </div>
                            <div class="card-footer">
                                <button id="addBtn" class="btn btn-success" value="Create"><i class="fas fa-cart-plus"></i> Make Sales</button>  
                                <button id="quotationBtn" class="btn btn-secondary d-none" value="Create"><i class="fas fa-file-invoice"></i> Make Quotation</button>  
                                <div id="loader" style="display: none;">
                                    <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                    Loading...
                                </div> 
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

@include('admin.inc.modal.whole_saler_modal')
@include('admin.inc.modal.size_modal')
@include('admin.inc.modal.color_modal')

@endsection

@section('script')

<script>
  $(document).on('input', '.quantity', function () {
      var max = parseInt($(this).data('max-quantity'));
      var val = parseInt($(this).val());
  
      if (val > max) {
          $(this).val(max);
      } else if (val < 1 ) {
          $(this).val(1);
      }
  });
</script>
  
<script>
    $(document).ready(function() {
        function updateSummary() {
            var itemTotalAmount = 0;

            $('#productTable tbody tr').each(function () {
                var quantity = parseFloat($(this).find('input.quantity').val()) || 0;
                var unitPrice = parseFloat($(this).find('input.price_per_unit').val()) || 0;

                var totalPrice = (quantity * unitPrice).toFixed(2);
                $(this).find('td:eq(3)').text(totalPrice);

                itemTotalAmount += parseFloat(totalPrice) || 0;
            });

            $('#item_total_amount').val(itemTotalAmount.toFixed(2) || '0.00');

            var vatPercent = parseFloat($('#vat_percent').val()) || 0;
            var totalVatAmount = (itemTotalAmount * vatPercent / 100).toFixed(2);
            $('#vat').val(totalVatAmount || '0.00');

            var discount = parseFloat($('#discount').val()) || 0;
            var netAmount = itemTotalAmount - discount + parseFloat(totalVatAmount);
            $('#net_amount').val(netAmount.toFixed(2) || '0.00');
        }

        $('#addProductBtn').click(function () {
            var warehouseId = $('#warehouse_id').val();
            var selectedProduct = $('#product_id option:selected');
            var productId = selectedProduct.val();
            var productName = selectedProduct.data('name');

            if (!warehouseId) {
                swal({
                    text: 'Please select a warehouse.',
                    icon: "error",
                    button: "OK"
                });
                return;
            }

            if (!productId) {
                swal({
                    text: 'Please select a product.',
                    icon: "error",
                    button: "OK"
                });
                return;
            }

            $.ajax({
                url: '/admin/get-stock-history',
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    warehouse_id: warehouseId,
                    product_id: productId
                },
                success: function (response) {
                    if (Array.isArray(response.stockHistories) && response.stockHistories.length > 0) {
                        response.stockHistories.forEach(function (stock) {
                            var stockHistoryId = stock.id;
                            var sellingPrice = !isNaN(parseFloat(stock.selling_price)) ? parseFloat(stock.selling_price).toFixed(2) : '0.00';

                            var availableQty = stock.available_qty;

                            var productExists = false;
                            $('#productTable tbody tr').each(function () {
                                var existingProductId = $(this).data('product-id');
                                var existingStockHistoryId = $(this).find('input[name="stock_history_id[]"]').data('stock-history-id');
                                if (productId == existingProductId && stockHistoryId == existingStockHistoryId) {
                                    productExists = true;
                                    return false;
                                }
                            });

                            if (productExists) {
                                return;
                            }

                            var productRow = `<tr data-product-id="${productId}">
                                <td>${productName}
                                    <input type="hidden" name="product_id[]" value="${productId}">
                                    <input type="hidden" name="stock_history_id[]" value="${stockHistoryId}" data-stock-history-id="${stockHistoryId}">
                                </td>
                                <td><input type="number" class="form-control quantity" name="quantity[]" value="1" min="1" max="${availableQty}" data-max-quantity="${availableQty}" /></td>
                                <td><input type="number" class="form-control price_per_unit" name="price_per_unit[]" value="${sellingPrice}" step="0.01" /></td>
                                <td class="total_price">${sellingPrice}</td>
                                <td><button type="button" class="btn btn-sm btn-danger remove-product">Remove</button></td>
                            </tr>`;

                            $('#productTable tbody').append(productRow);
                        });

                        $('#product_id').val(null).trigger('change');
                        $('#stock_history').val('').prop('disabled', true);
                        $('#quantity').val('');
                        $('#price_per_unit').val('');

                        updateSummary();
                    } else {
                        swal({
                            text: 'No stock history found for the selected product in this warehouse.',
                            icon: "warning",
                            button: "OK"
                        });
                    }
                },
                error: function () {
                    swal({
                        text: 'An error occurred while fetching stock history.',
                        icon: "error",
                        button: "OK"
                    });
                }
            });
        });

        $(document).on('click', '.remove-product', function() {
            $(this).closest('tr').remove();
            updateSummary();
            $('#product_id').val(null).trigger('change');
        });
        
        $(document).on('input', '.quantity', function() {
            if ($(this).val() < 1) {
                $(this).val(1);
            }
        });

        $(document).on('input', '#productTable input.quantity, #productTable input.price_per_unit, #vat_percent, #discount', function () {
            updateSummary();
        });

        $('#addBtn').on('click', function(e) {
            e.preventDefault();

            $(this).attr('disabled', true);
            $('#quotationBtn').attr('disabled', true);
            $('#loader').show();

            var formData = $(this).serializeArray();
            var products = [];
            
            formData.push({ name: 'purchase_date', value: $('#purchase_date').val() });
            formData.push({ name: 'user_id', value: $('#user_id').val() });
            formData.push({ name: 'vehicle_number', value: $('#vehicle_number').val() });
            formData.push({ name: 'destination', value: $('#destination').val() });
            formData.push({ name: 'warehouse_id', value: $('#warehouse_id').val() });
            formData.push({ name: 'payment_method', value: $('#payment_method').val() });
            formData.push({ name: 'ref', value: $('#ref').val() });
            formData.push({ name: 'remarks', value: $('#remarks').val() });
            formData.push({ name: 'item_total_amount', value: $('#item_total_amount').val() });
            formData.push({ name: 'vat', value: $('#vat').val() });
            formData.push({ name: 'vat_percent', value: $('#vat_percent').val() });
            formData.push({ name: 'discount', value: $('#discount').val() });
            formData.push({ name: 'net_amount', value: $('#net_amount').val() });
            formData.push({ name: 'cash_payment', value: $('#cash_payment').val() });
            formData.push({ name: 'bank_payment', value: $('#bank_payment').val() });

            $('#productTable tbody tr').each(function() {
                var productId = $(this).find('input[name="product_id[]"]').val();
                var stockHistoryId = $(this).find('input[name="stock_history_id[]"]').val();
                var quantity = parseFloat($(this).find('input.quantity').val()) || 0;
                var unitPrice = parseFloat($(this).find('input.price_per_unit').val()) || 0;
                var totalPrice = (quantity * unitPrice).toFixed(2);

                products.push({
                    product_id: productId,
                    stock_history_id: stockHistoryId,
                    quantity: quantity,
                    unit_price: unitPrice,
                    total_price: totalPrice
                });
            });

            formData = formData.filter(function(item) {
                return item.name !== 'product_id' && item.name !== 'quantity' && item.name !== 'price_per_unit';
            });

            formData.push({ name: 'products', value: JSON.stringify(products) });

            // console.log(formData);

            $.ajax({
                url: '/admin/in-house-sell',
                method: 'POST',
                data: formData,
                dataType: 'json',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    swal({
                        text: "Created Successfully",
                        icon: "success",
                        button: {
                            text: "OK",
                            className: "swal-button--confirm"
                        }
                    }).then(() => {
                        window.location.href = response.pdf_url;
                        setTimeout(function() {
                            location.reload();
                        }, 1000);
                    });
                },
                error: function(xhr) {
                    swal({
                        text: xhr.responseJSON.message,
                        icon: "error",
                        button: {
                            text: "OK",
                            className: "swal-button--error"
                        }
                    });
                    console.log(xhr.responseText);
                },
                complete: function() {
                    $('#loader').hide();
                    $('#addBtn').attr('disabled', false);
                    $('#quotationBtn').attr('disabled', false);
                }
            });
        });

        $('#quotationBtn').on('click', function(e) {
            e.preventDefault();

            $(this).attr('disabled', true);
            $('#addBtn').attr('disabled', true);
            $('#loader').show();

            var formData = $(this).serializeArray();
            var products = [];

            formData.push({ name: 'purchase_date', value: $('#purchase_date').val() });
            formData.push({ name: 'user_id', value: $('#user_id').val() });
            formData.push({ name: 'payment_method', value: $('#payment_method').val() });
            formData.push({ name: 'ref', value: $('#ref').val() });
            formData.push({ name: 'remarks', value: $('#remarks').val() });
            formData.push({ name: 'item_total_amount', value: $('#item_total_amount').val() });
            formData.push({ name: 'vat', value: $('#vat').val() });
            formData.push({ name: 'vat_percent', value: $('#vat_percent').val() });
            formData.push({ name: 'discount', value: $('#discount').val() });
            formData.push({ name: 'net_amount', value: $('#net_amount').val() });

            $('#productTable tbody tr').each(function() {
                var productId = $(this).find('input[name="product_id[]"]').val();
                var stockHistoryId = $(this).find('input[name="stock_history_id[]"]').val();
                var quantity = parseFloat($(this).find('input.quantity').val()) || 0;
                var unitPrice = parseFloat($(this).find('input.price_per_unit').val()) || 0;
                var totalPrice = (quantity * unitPrice).toFixed(2);

                products.push({
                    product_id: productId,
                    stock_history_id: stockHistoryId,
                    quantity: quantity,
                    unit_price: unitPrice,
                    total_price: totalPrice
                });
            });

            formData.push({ name: 'vat', value: $('#vat').val() });

            formData = formData.filter(function(item) {
                return item.name !== 'product_id' && item.name !== 'quantity' && item.name !== 'price_per_unit';
            });

            formData.push({ name: 'products', value: JSON.stringify(products) });

            // console.log(formData);

            $.ajax({
                url: '/admin/make-quotation',
                method: 'POST',
                data: formData,
                dataType: 'json',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    swal({
                        text: "Quotation created successfully",
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
                    swal({
                        text: xhr.responseJSON.message,
                        icon: "error",
                        button: {
                            text: "OK",
                            className: "swal-button--error"
                        }
                    })
                    console.log(xhr.responseText);
                },
                complete: function() {
                    $('#loader').hide();
                    $('#quotationBtn').attr('disabled', false);
                    $('#addBtn').attr('disabled', false);
                }
            });
        });

        $('#cash_payment').on('keyup', function() {
            paymentCheck($(this).val(), 'Cash Payment');
        });

        $('#bank_payment').on('keyup', function() {
            paymentCheck($(this).val(), 'Bank Payment');
        });

        function paymentCheck(payment, paymentType) {
            var netAmount = parseFloat($('#net_amount').val()) || 0;
            var paymentValue = parseFloat(payment) || 0;

            if (paymentValue > netAmount) {
                $('.errmsg').text(paymentType + ' is greater than Net Amount');
                $('#cash_payment').val('0.00');
                $('#bank_payment').val('0.00');
            } else {
                $('.errmsg').text('');
            }
        }

        $('#warehouse_id, #user_id, #product_id').select2({
            placeholder: "Select...",
            allowClear: true,
            width: '100%'
        });
    });
</script>

<script>
    $(document).ready(function() {

        $('#product_id').change(function() {
            var selectedProduct = $(this).find(':selected');
            var pricePerUnit = selectedProduct.data('price');
            $('#quantity').val(1);
            
            if(pricePerUnit) {
                $('#price_per_unit').val(pricePerUnit);
            } else {
                $('#price_per_unit').val('');
            }
        });
    });
</script>

<script>
    $(document).ready(function() {
        $('#saveWholeSalerBtn').on('click', function() {
            var formData = new FormData($('#newWholeSalerForm')[0]);
            formData.append('_token', '{{ csrf_token() }}');

            $.ajax({
                type: 'POST',
                url: "{{ route('customer.store') }}",
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    console.log(response);
                    $('#user_id').append(`<option value="${response.id}" selected>${$('#name').val()} ${$('#surname').val() || ''}</option>`);
                    $('#newWholeSalerForm')[0].reset();
                    $('#newWholeSalerModal').modal('hide');

                    swal({
                        text: "Created successfully",
                        icon: "success",
                        button: {
                            text: "OK",
                            className: "swal-button--confirm"
                        }
                    });
                },
                error: function(xhr) {
                    swal({
                        text: xhr.responseJSON.message,
                        icon: "error",
                        button: {
                            text: "OK",
                            className: "swal-button--error"
                        }
                    })
                    // console.error(xhr.responseText);
                }
            });
        });

        $('#saveColorBtn').click(function() {
            let colorName = $('#color_name').val();
            let color_code = $('#color_code').val();
            let price = $('#color_price').val();

            $.ajax({
                url: '{{ route('color.store') }}',
                type: 'POST',
                data: {
                    color_name: colorName,
                    color_code: color_code,
                    price: price,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.success) {
                        swal({
                            text: "Color added successfully",
                            icon: "success",
                            button: {
                                text: "OK",
                                className: "swal-button--confirm"
                            }
                        }).then(() => {
                            $('#color').append(`<option value="${response.data.color}">${response.data.color}</option>`);
                            $('#addColorModal').modal('hide');
                            $('#newColorForm')[0].reset();
                        });
                    } else {
                        swal({
                            text: "Failed to add color",
                            icon: "error",
                            button: {
                                text: "OK",
                                className: "swal-button--error"
                            }
                        });
                    }
                },
                error: function(xhr) {
                    let errorMessage = "Error adding color. Please try again.";
                    
                    if (xhr.responseJSON && xhr.responseJSON.errors) {
                        errorMessage = Object.values(xhr.responseJSON.errors).join("\n");
                    }
                    
                    swal({
                        text: errorMessage,
                        icon: "error",
                        button: {
                            text: "OK",
                            className: "swal-button--error"
                        }
                    });
                }
            });
        });

        $('#saveSizeBtn').click(function() {

            let size = $('#size_name').val();
            let price = $('#size_price').val();

            $.ajax({
                url: '{{ route('size.store') }}',
                type: 'POST',
                data: {
                    size: size,
                    price: price,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.success) {
                        swal({
                            text: "Size added successfully",
                            icon: "success",
                            button: {
                                text: "OK",
                                className: "swal-button--confirm"
                            }
                        }).then(() => {
                            $('#size').append(`<option value="${response.data.size}">${response.data.size}</option>`);
                            
                            $('#addSizeModal').modal('hide');
                            $('#newSizeForm')[0].reset();
                        });
                    } else {
                        swal({
                            text: "Failed to add size",
                            icon: "error",
                            button: {
                                text: "OK",
                                className: "swal-button--error"
                            }
                        });
                    }
                },
                error: function(xhr) {
                    let errorMessage = "Error adding size. Please try again.";
                    
                    if (xhr.responseJSON && xhr.responseJSON.errors) {
                        errorMessage = Object.values(xhr.responseJSON.errors).join("\n");
                    }
                    
                    swal({
                        text: errorMessage,
                        icon: "error",
                        button: {
                            text: "OK",
                            className: "swal-button--error"
                        }
                    });
                }
            });
        });
    });
</script>

@endsection