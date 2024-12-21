@extends('admin.layouts.admin')

@section('content')

<section class="content pt-3" id="flashSellFormContainer">
    <div class="container-fluid">
        <div class="row justify-content-md-center">
            <div class="col-md-12">
                <div class="card card-secondary">
                    <div class="card-header">
                        <h3 class="card-title" id="cardTitle">Edit Flash Sell</h3>
                    </div>
                    <div class="card-body">
                        <div class="ermsg"></div>
                        <form id="flashSellForm">
                            @csrf
                            <input type="hidden" name="flash_sell_id" value="{{ $flashSell->id }}">
                            <div class="row">
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label for="flash_sell_name">Flash Sell Name <span style="color: red;">*</span></label>
                                        <input type="text" class="form-control" id="flash_sell_name" name="flash_sell_name" placeholder="Enter flash sell name" value="{{ $flashSell->flash_sell_name }}">
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label for="flash_sell_title">Flash Sell Title <span style="color: red;">*</span></label>
                                        <input type="text" class="form-control" id="flash_sell_title" name="flash_sell_title" placeholder="Enter flash sell title" value="{{ $flashSell->flash_sell_title }}">
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label for="start_date">Start Date <span style="color: red;">*</span></label>
                                        <input type="date" class="form-control" id="start_date" name="start_date" value="{{ $flashSell->start_date }}">
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label for="end_date">End Date <span style="color: red;">*</span></label>
                                        <input type="date" class="form-control" id="end_date" name="end_date" value="{{ $flashSell->end_date }}">
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="flash_sell_description">Description <span style="color: red;">*</span></label>
                                        <textarea class="form-control" id="flash_sell_description" name="flash_sell_description" rows="3" placeholder="Enter flash sell description">{{ $flashSell->flash_sell_description }}</textarea>
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label for="flash_sell_image">Flash Sell Image <span style="color: red;">*</span></label>
                                        <input type="file" class="form-control-file" id="flash_sell_image" name="flash_sell_image" onchange="previewImage(event)">
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div id="image-preview" class="mt-2">
                                        @if($flashSell->flash_sell_image)
                                            <img id="preview" src="{{ asset('images/flash_sell/' . $flashSell->flash_sell_image) }}" style="max-height: 100px; width: auto;">
                                        @else
                                            <img id="preview" src="" style="max-height: 100px; width: auto; display: none;">
                                        @endif
                                    </div>
                                </div>

                                <div class="col-sm-5">
                                    <div class="form-group">
                                        <label for="product_id">Choose Product <span style="color: red;">*</span></label>
                                        <select class="form-control" id="product_id" name="product_id">
                                            <option value="">Select...</option>
                                            @foreach($products as $product)
                                                <option value="{{ $product->id }}" data-name="{{ $product->name }}" data-price="{{ $product->price }}">{{ $product->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-2">
                                    <div class="form-group">
                                        <label for="old_price">Old Price</label>
                                        <input type="number" step="0.01" class="form-control" id="old_price" name="old_price" placeholder="Enter old price">
                                    </div>
                                </div>
                                <div class="col-sm-2">
                                    <div class="form-group">
                                        <label for="flash_sell_price">Flash Sell Price <span style="color: red;">*</span></label>
                                        <input type="number" step="0.01" class="form-control" id="flash_sell_price" name="flash_sell_price" placeholder="Enter flash sell price">
                                    </div>
                                </div> 
                                <div class="col-sm-2">
                                    <div class="form-group">
                                        <label for="quantity">Quantity <span style="color: red;">*</span></label>
                                        <input type="number" class="form-control" id="quantity" name="quantity" placeholder="Enter quantity">
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
                                                <th>Product Name</th>
                                                <th>Old Price</th>
                                                <th>Flash Sell Price</th>
                                                <th>Quantity</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody id="productTableBody">
                                            @foreach ($flashSell->flashSellDetails as $detail)
                                                <tr data-product-id="{{ $detail->product->id }}">
                                                    <td>{{ $detail->product->name }}</td>
                                                    <td>
                                                        <input type="number" step="0.01" class="form-control old-price-input" value="{{ $detail->old_price }}">
                                                    </td>
                                                    <td>
                                                        <input type="number" step="0.01" class="form-control flash-sell-price-input" value="{{ $detail->flash_sell_price }}">
                                                    </td>
                                                    <td>
                                                        <input type="number" class="form-control quantity-input" value="{{ $detail->quantity }}" min="1">
                                                    </td>
                                                    <td>
                                                        <button type="button" class="btn btn-danger btn-sm remove-product">Remove</button>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>

                            </div>
                            <div class="card-footer">
                                <button type="button" id="updateFlashSellBtn" class="btn btn-secondary"><i class="fas fa-update"></i> Update</button>
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
        function productExists(productId) {
            let exists = false;
            $('#productTableBody tr').each(function() {
                if ($(this).data('product-id') == productId) {
                    exists = true;
                    return false;
                }
            });
            return exists;
        }

        $('#product_id').select2({
            placeholder: "Select product...",
            allowClear: true,
            width: '100%'
        }).on('change', function() {
            const selectedOption = $(this).find(':selected');
            const price = selectedOption.data('price');
            $('#old_price').val(price);
        });

        $('#flash_sell_image').change(function() {
            const input = this;
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    $('#preview').attr('src', e.target.result).show();
                };
                reader.readAsDataURL(input.files[0]);
            }
        });

        $('#addProductBtn').click(function() {
            const productId = $('#product_id').val();
            const productName = $('#product_id option:selected').text();
            const oldPrice = parseFloat($('#old_price').val());
            const flashSellPrice = parseFloat($('#flash_sell_price').val());
            const quantity = parseInt($('#quantity').val());

            if (!productId || !oldPrice || !flashSellPrice || !quantity || quantity < 1) {
                alert('Please ensure all fields are filled out correctly.');
                return;
            }

            if (productExists(productId)) {
                alert('Product already exists in the table.');
                return;
            }

            const newRow = `
                <tr data-product-id="${productId}">
                    <td>${productName}</td>
                    <td><input type="number" step="0.01" class="form-control old-price-input" value="${oldPrice.toFixed(2)}"></td>
                    <td><input type="number" step="0.01" class="form-control flash-sell-price-input" value="${flashSellPrice.toFixed(2)}"></td>
                    <td><input type="number" class="form-control quantity-input" value="${quantity}" min="1"></td>
                    <td>
                        <button type="button" class="btn btn-danger btn-sm remove-product">Remove</button>
                    </td>
                </tr>
            `;
            $('#productTableBody').append(newRow);

            $('#product_id').val('').trigger('change');
            $('#old_price').val('');
            $('#flash_sell_price').val('');
            $('#quantity').val('');
        });

        $(document).on('click', '.remove-product', function() {
            $(this).closest('tr').remove();
        });

        $(document).on('change', '.quantity-input', function() {
            let quantity = parseInt($(this).val());
            if (quantity < 1 || isNaN(quantity)) {
                quantity = 1;
                alert('Quantity cannot be less than 1.');
            }
            $(this).val(quantity);
        });

        $('#updateFlashSellBtn').click(function(event) {
            event.preventDefault();

            const products = [];
            $('#productTableBody tr').each(function() {
                const row = $(this);
                const productId = row.data('product-id');
                const oldPrice = parseFloat(row.find('.old-price-input').val());
                const flashSellPrice = parseFloat(row.find('.flash-sell-price-input').val());
                const quantity = parseInt(row.find('.quantity-input').val());

                products.push({
                    product_id: productId,
                    old_price: oldPrice,
                    flash_sell_price: flashSellPrice,
                    quantity: quantity
                });
            });

            if (products.length === 0) {
                alert('Please add at least one product.');
                return;
            }

            const formData = new FormData($('#flashSellForm')[0]);

            products.forEach(product => {
                formData.append('products[]', JSON.stringify(product));
            });

            const flashSellId = $('#flash_sell_id').val();
            if (flashSellId) {
                formData.append('flash_sell_id', flashSellId);
            }

            const flashSellImage = $('#flash_sell_image')[0].files[0];
            if (flashSellImage) {
                formData.append('flash_sell_image', flashSellImage);
            }

            $.ajax({
                url: '/admin/update-flash-sell',
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    swal({
                        text: "Updated successfully",
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
                    console.log(xhr.responseText);
                }
            });
        });
    });

</script>

@endsection

