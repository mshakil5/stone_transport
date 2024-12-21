@extends('admin.layouts.admin')

@section('content')

<!-- Main content -->
<section class="content" id="newBtnSection">
    <div class="container-fluid">
        <div class="row">
            <div class="col-2">
                <button type="button" class="btn btn-secondary my-3" id="newBtn">Add new</button>
            </div>
        </div>
    </div>
</section>
<!-- /.content -->


<section class="content mt-3" id="addThisFormContainer">
    <div class="container-fluid">
        <div class="row justify-content-md-center">
            <div class="col-md-10">
                <div class="card card-secondary">
                    <div class="card-header">
                        <h3 class="card-title" id="cardTitle">Add New Whole Sale Product</h3>
                    </div>
                    <div class="card-body">
                        <div class="ermsg" style="color: red;"></div>
                        <form id="createThisForm">
                            <input type="hidden" class="form-control" id="codeid" name="codeid">
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label for="product_ids">Select Product</label>
                                    <select class="form-control select2" id="product_id" name="product_id">
                                        @foreach($products as $product)
                                        <option value="{{ $product->id }}">{{ $product->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="size_ids">Sizes</label>
                                    <button type="button" class="btn btn-success btn-sm ml-2" id="addSizeBtn" data-toggle="modal" data-target="#addSizeModal"> <i class="fas fa-plus"></i> Add</button>
                                    <select class="form-control select2" id="size_ids" name="size_ids[]" multiple="multiple" data-placeholder="Select sizes">
                                        @foreach($sizes as $size)
                                            <option value="{{ $size->id }}">{{ $size->size }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label for="short_description">Short Description</label>
                                    <textarea class="form-control" id="short_description" name="short_description" rows="3" placeholder="Enter bundle product short description"></textarea>
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="long_description">Long Description</label>
                                    <textarea class="form-control" id="long_description" name="long_description" rows="3" placeholder="Enter bundle product long description"></textarea>
                                </div>
                            </div>

                            <div class="form-row">

                                <div class="form-group col-md-6">
                                    <label for="feature-img">Feature Image</label>
                                    <input type="file" class="form-control-file" id="feature-img" name="feature_image" accept="image/*">
                                    <img id="preview-image" src="#" alt="" style="max-width: 200px; width: 100%; height: auto; margin-top: 20px;">
                                </div>

                                <div class="form-group col-md-1">
                                    <label for="is_featured">Featured</label>
                                    <input type="checkbox" class="form-control" id="is_featured" name="is_featured">
                                </div>
                                <div class="form-group col-md-1">
                                    <label for="is_recent">Recent</label>
                                    <input type="checkbox" class="form-control" id="is_recent" name="is_recent">
                                </div>
                                <div class="form-group col-md-1">
                                    <label for="is_new_arrival">New Arriv.</label>
                                    <input type="checkbox" class="form-control" id="is_new_arrival" name="is_new_arrival">
                                </div>
                                <div class="form-group col-md-1">
                                    <label for="is_top_rated">Top Rated</label>
                                    <input type="checkbox" class="form-control" id="is_top_rated" name="is_top_rated">
                                </div>
                                <div class="form-group col-md-1">
                                    <label for="is_popular">Popular</label>
                                    <input type="checkbox" class="form-control" id="is_popular" name="is_popular">
                                </div>
                                <div class="form-group col-md-1">
                                    <label for="is_trending">Trending</label>
                                    <input type="checkbox" class="form-control" id="is_trending" name="is_trending">
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group col-md-5">
                                    <label for="color_id">Select Color</label>
                                    <button type="button" class="btn btn-success btn-sm ml-2" id="addColorBtn" data-toggle="modal" data-target="#addColorModal"><i class="fas fa-plus"></i> Add</button>
                                    <select class="form-control" name="color_id[]" id="color_id">
                                        <option value="">Choose Color</option>
                                        @foreach($colors as $color)
                                        <option value="{{ $color->id }}" style="background-color: {{ $color->color_code }};">
                                            {{ $color->color }} ({{ $color->color_code }})
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group col-md-5">
                                    <label for="image">Select Image</label>
                                    <input type="file" class="form-control" name="image[]" accept="image/*">
                                </div>
                                <div class="form-group col-md-1">
                                    <label>Action</label>
                                    <button type="button" class="btn btn-success add-row"><i class="fas fa-plus"></i></button>
                                </div>
                            </div>
                            <div id="dynamic-rows"></div>

                        </form>
                    </div>
                    <div class="card-footer">
                        <button type="submit" id="addBtn" class="btn btn-secondary" value="Create">Create</button>
                        <button type="submit" id="FormCloseBtn" class="btn btn-default">Cancel</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="content mt-3" id="contentContainer">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card card-secondary">
                    <div class="card-header">
                        <h3 class="card-title">Whole Sale Products</h3>
                    </div>
                    <div class="card-body">
                        <table id="bundleProductsTable" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Feature Image</th>
                                    <th>Short Description</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($wholeSaleProducts as $wholeSaleProduct)
                                <tr>
                                    <td>{{ $wholeSaleProduct->product->name }}</td>
                                    <td><img src="{{ asset($wholeSaleProduct->feature_image) }}" style="max-width: 100px; max-height: 100px;"></td>
                                    <td>{!! $wholeSaleProduct->short_description !!}</td>
                                    <td>
                                        <a class="priceBtn" rid="{{ $wholeSaleProduct->id }}" data-toggle="modal" data-target="#priceModal">
                                            <i class="fa fa-money" style="color: #4CAF50; font-size:16px;"></i>
                                        </a>
                                        <a class="EditBtn" rid="{{ $wholeSaleProduct->id }}">
                                            <i class="fa fa-edit" style="color: #2196f3; font-size:16px;"></i>
                                        </a>
                                        <a class="deleteBtn" rid="{{ $wholeSaleProduct->id }}">
                                            <i class="fa fa-trash-o" style="color: red; font-size:16px;"></i>
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

<!-- Price Modal -->
<div class="modal fade" id="priceModal" tabindex="-1" role="dialog" aria-labelledby="priceModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="priceModalLabel">Manage Prices</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="priceForm">
                    <input type="hidden" id="whole_sale_product_id" name="whole_sale_product_id">
                    <div id="priceFieldsContainer">
                    </div>
                    <button type="button" id="addPriceField" class="btn btn-secondary">Add More</button>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" id="savePrices" class="btn btn-primary">Save Prices</button>
            </div>
        </div>
    </div>
</div>

<!-- Add Size Modal -->
<div class="modal fade" id="addSizeModal" tabindex="-1" role="dialog" aria-labelledby="addSizeModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addSizeModalLabel">Add New Size</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="newSizeForm">
                    <div class="col-sm-12">
                        <div class="form-group">
                            <label for="size_name">Size</label>
                            <input type="text" class="form-control" id="size_name" name="size_name" placeholder="Enter size">
                        </div>
                    </div>
                    <div class="col-sm-12">
                        <div class="form-group">
                            <label for="size_price">Price</label>
                            <input type="number" class="form-control" id="size_price" name="size_price" placeholder="Enter price">
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="saveSizeBtn">Save Size</button>
            </div>
        </div>
    </div>
</div>

<!-- Add Color Modal -->
<div class="modal fade" id="addColorModal" tabindex="-1" role="dialog" aria-labelledby="addColorModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content modal-lg">
            <div class="modal-header">
                <h5 class="modal-title" id="addColorModalLabel">Add New Color</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="newColorForm">
                    <div class="form-group">
                        <label for="color_name">Color</label>
                        <input type="text" class="form-control" id="color_name" name="color_name" placeholder="Enter color">
                    </div>
                    <div class="form-group">
                        <label for="color_code">Color Code</label>
                        <input type="color" class="form-control" id="color_code" name="color_code" placeholder="Enter color code">
                    </div>
                    <div class="form-group">
                        <label for="color_price">Price</label>
                        <input type="number" class="form-control" id="color_price" name="color_price" placeholder="Enter price">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="saveColorBtn">Save Color</button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('script')

<script>
    $(document).ready(function() {
        $(document).on('click', '.add-row', function() {
            let newRow = `
            <div class="form-row dynamic-row">
                <div class="form-group col-md-5">
                    <label for="color_id">Select Color</label>
                    <select class="form-control" name="color_id[]" id="color_id">
                        <option value="">Choose Color</option>
                        @foreach($colors as $color)
                            <option value="{{ $color->id }}" style="background-color: {{ $color->color_code }};">
                                {{ $color->color }} ({{ $color->color_code }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group col-md-5">
                    <label for="image">Select Image</label>
                    <input type="file" class="form-control" name="image[]" accept="image/*">
                </div>
                <div class="form-group col-md-1">
                    <label>Action</label>
                    <button type="button" class="btn btn-danger remove-row"><i class="fas fa-minus"></i></button>
                </div>
            </div>`;
            
            $('#dynamic-rows').append(newRow);
        });

        $(document).on('click', '.remove-row', function() {
            $(this).closest('.dynamic-row').remove();
        });

        $("#feature-img").change(function(e){
            var reader = new FileReader();
            reader.onload = function(e){
                $("#preview-image").attr("src", e.target.result);
            };
            reader.readAsDataURL(this.files[0]);
        });

        $(document).on('click', '#addBtn', function(e) {
            e.preventDefault();
            
            var formData = new FormData($('#createThisForm')[0]);
            
            $.ajax({
                url: '{{ route("whole_sale_product.store") }}',
                method: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                cache: false,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    swal({
                        text: "Created successfully",
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
                    swal({
                        text: xhr.responseJSON.message,
                        icon: "error",
                        button: {
                            text: "OK",
                            className: "swal-button--confirm"
                        }
                    })
                    console.error(xhr.responseText);
                }
            });
        });

        $(document).on('click', '.deleteBtn', function(e) {
            e.preventDefault();

            let productId = $(this).attr('rid');
            if (confirm('Are you sure you want to delete this product?')) {
                $.ajax({
                    url: '{{ route("whole_sale_product.destroy", ":id") }}'.replace(':id', productId),
                    type: 'DELETE',
                    data: {
                        _token: '{{ csrf_token() }}',
                    },
                    success: function(response) {
                        swal({
                            text: "Deleted successfully",
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
                        swal({
                            text: xhr.responseJSON.message,
                            icon: "error",
                            button: {
                                text: "OK",
                                className: "swal-button--confirm"
                            }
                        })
                    }
                });
            }
        });

    });
</script>

<script>
    $(document).ready(function () {
        $("#addThisFormContainer").hide();

        $("#newBtn").click(function(){
            clearForm();
            $("#newBtn").hide(100);
            $("#addThisFormContainer").show(300);
        });

        $("#FormCloseBtn").click(function(){
            $("#addThisFormContainer").hide(200);
            $("#newBtn").show(100);
            clearForm();
            $('.ermsg').empty();
        });

        $("#bundleProductsTable").DataTable({
            "responsive": true,
            "lengthChange": false,
            "autoWidth": false,
            "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"]
        }).buttons().container().appendTo('#bundleProductsTable_wrapper .col-md-6:eq(0)');

        $('.select2').select2({
            placeholder: "Select sizes",
            width: '100%'
        });

        $('#long_description, #short_description').summernote({
            height: 100,
        });

        function clearForm(){
            $('#createThisForm')[0].reset();
            $("#addBtn").val('Create').text('Create');
            $("#cardTitle").text('Add new data');
            $('#preview-image').attr('src', '#');
            $('#feature-img').val('');
            $('#size_ids').val(null).trigger('change');
            $("#long_description").summernote('code', '');
            $("#short_description").summernote('code', '');
        }

    });
</script>

<script>
    $(document).on('click', '.priceBtn', function() {
        var productId = $(this).attr('rid');
        $('#whole_sale_product_id').val(productId);
        $('#priceFieldsContainer').html('');

        $.ajax({
            url: '/admin/whole-sale-product/prices',
            method: 'GET',
            data: { product_id: productId },
            success: function(data) {
                if (data && data.length) {
                    data.forEach(function(price) {
                        addPriceField(price.min_quantity, price.max_quantity, price.price, price.id);
                    });
                } else {
                    addPriceField();
                }
            },
            error: function(xhr, status, error) {
                console.error(xhr.responseText);
            }
        });
    });

    $('#addPriceField').on('click', function() {
        addPriceField();
    });

    function addPriceField(minQuantity = '', maxQuantity = '', price = '', priceId = '') {
        var priceFieldHtml = `
            <div class="row price-field align-items-center mb-2">
                <div class="col-4">
                    <div class="form-group">
                        <label for="min_quantity">Min Quantity</label>
                        <input type="number" class="form-control" name="min_quantity[]" value="${minQuantity}" required>
                    </div>
                </div>
                <div class="col-4">
                    <div class="form-group">
                        <label for="max_quantity">Max Quantity</label>
                        <input type="number" class="form-control" name="max_quantity[]" value="${maxQuantity}" required>
                    </div>
                </div>
                <div class="col-2">
                    <div class="form-group">
                        <label for="price">Price</label>
                        <input type="number" class="form-control" name="price[]" step="0.01" value="${price}" required>
                    </div>
                </div>
                <input type="hidden" name="price_id[]" value="${priceId}">
                <div class="col-2 text-center">
                    <button type="button" class="btn btn-danger remove-price-field">
                        <i class="fa fa-minus"></i>
                    </button>
                </div>
            </div>`;

        $('#priceFieldsContainer').append(priceFieldHtml);
    }

    $(document).on('click', '.remove-price-field', function() {
        $(this).closest('.price-field').remove();
    });

    function validateQuantities() {
        let isValid = true;
        $('.price-field').each(function() {
            var minQuantity = parseInt($(this).find('input[name="min_quantity[]"]').val());
            var maxQuantity = parseInt($(this).find('input[name="max_quantity[]"]').val());

            if (minQuantity >= maxQuantity) {
                isValid = false;
                swal({
                    text: "Max quantity must be higher than min quantity",
                    icon: "error",
                    button: {
                        text: "OK",
                        className: "swal-button--error"
                    }
                });
                return false;
            }
        });
        return isValid;
    }

    $('#savePrices').on('click', function() {
        if (!validateQuantities()) {
            return;
        }

        var formData = $('#priceForm').serialize();
        formData += '&whole_sale_product_id=' + $('#whole_sale_product_id').val(); 
        $.ajax({
            url: '/admin/whole-sale-product/prices',
            method: 'POST',
            data: formData,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                swal({
                    text: "Prices updated successfully",
                    icon: "success",
                    button: {
                        text: "OK",
                        className: "swal-button--confirm"
                    }
                });
                $('#priceModal').modal('hide');
            },
            error: function(xhr) {
                alert('Error saving prices: ' + xhr.responseText);
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
                        $('#size_ids').append(`<option value="${response.data.id}">${response.data.size}</option>`);
                        
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
                        $('#color_id').append(`<option value="${response.data.id}" style="background-color: ${response.data.color_code};">${response.data.color} (${response.data.color_code})</option>`);
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
</script>

@endsection