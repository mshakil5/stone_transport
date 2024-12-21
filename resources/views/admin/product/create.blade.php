@extends('admin.layouts.admin')

@section('content')

<section class="content pt-3" id="addThisFormContainer">
    <div class="container-fluid">
        <div class="row justify-content-md-center">
            <div class="col-md-12">

                <div class="mb-3">
                    <a href="{{ route('allproduct') }}" class="btn btn-secondary">
                    <i class="fa fa-arrow-left"></i> Back
                    </a>
                </div>

                <div class="card card-secondary">
                    <div class="card-body">
                        <div class="ermsg"></div>
                        <form id="createThisForm">
                            @csrf
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label for="name">Product Name <span style="color: red;">*</span></label>
                                    <input type="text" class="form-control" id="name" name="name" placeholder="Ex. Stylish Running Shoes">
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="price">Product Code <span style="color: red;">*</span></label>
                                    <input type="text" class="form-control" id="product_code" name="product_code" placeholder="Ex. PRD-12345">
                                    <span id="productCodeError" class="text-danger"></span>
                                </div>
                                <div class="form-group col-md-2 d-none">
                                    <label for="price">Price</label>
                                    <input type="number" class="form-control" id="price" name="price" placeholder="Ex. 1000">
                                </div>
                                <div class="form-group col-md-2 d-none">
                                    <label for="size_ids">Sizes</label>
                                    <span class="badge badge-success" style="cursor: pointer;" data-toggle="modal" data-target="#addSizeModal">Add New</span>
                                    <select class="form-control select2" id="size_ids" name="size_ids[]" multiple="multiple" data-placeholder="Select sizes">
                                        @foreach($sizes as $size)
                                        <option value="{{ $size->id }}">{{ $size->size }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group col-md-2 d-none">
                                    <label for="sku">Sku</label>
                                    <input type="number" class="form-control" id="sku" name="sku" placeholder="Ex. 123">
                                </div>
                            </div>

                            <div class="form-row d-none">
                                <div class="form-group col-md-6">
                                    <label for="short_description">Short Description <span style="color: red;">*</span></label>
                                    <textarea class="form-control" id="short_description" name="short_description"></textarea>
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="description">Long Description <span style="color: red;">*</span></label>
                                    <textarea class="form-control" id="long_description" name="long_description"></textarea>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group col-md-4">
                                    <label for="category">Category
                                        <span class="badge badge-success" style="cursor: pointer;" data-toggle="modal" data-target="#addCategoryModal">Add New</span>
                                    </label>
                                    <select class="form-control" id="category" name="category_id">
                                        <option value="">Select Category</option>
                                        @foreach($categories as $category)
                                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group col-md-4 d-none">
                                    <label for="subcategory">
                                        Sub Category
                                        <span class="badge badge-success" style="cursor: pointer;" data-toggle="modal" data-target="#addSubCategoryModal">Add New</span>
                                    </label>
                                    <select class="form-control" id="subcategory" name="sub_category_id">
                                        <option value="">Select Sub Category</option>
                                        @foreach($subCategories as $subcategory)
                                        <option class="subcategory-option category-{{ $subcategory->category_id }}" value="{{ $subcategory->id }}">{{ $subcategory->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group col-md-4 d-none">
                                    <label for="brand">
                                        Brand
                                        <span class="badge badge-success" style="cursor: pointer;" data-toggle="modal" data-target="#addBrandModal">Add New</span>
                                    </label>
                                    <select class="form-control" id="brand" name="brand_id">
                                        <option value="">Select Brand</option>
                                        @foreach($brands as $brand)
                                        <option value="{{ $brand->id }}">{{ $brand->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group col-md-4 d-none">
                                    <label for="model">Model
                                        <span class="badge badge-success" style="cursor: pointer;" data-toggle="modal" data-target="#addModelModal">Add New</span>
                                    </label>
                                    <select class="form-control" id="model" name="product_model_id">
                                        <option value="">Select Model</option>
                                        @foreach($product_models as $model)
                                        <option value="{{ $model->id }}">{{ $model->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group col-md-4">
                                    <label for="unit">
                                        Unit <span class="badge badge-success" style="cursor: pointer;" data-toggle="modal" data-target="#addUnitModal">Add New</span>
                                    </label>
                                    <select class="form-control" id="unit" name="unit_id">
                                        <option value="">Select Unit</option>
                                        @foreach($units as $unit)
                                        <option value="{{ $unit->id }}">{{ $unit->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group col-md-4">
                                    <label for="group">
                                        Group <span class="badge badge-success" style="cursor: pointer;" data-toggle="modal" data-target="#addGroupModal">Add New</span>
                                    </label>
                                    <select class="form-control" id="group" name="group_id">
                                        <option value="">Select Group</option>
                                        @foreach($groups as $group)
                                        <option value="{{ $group->id }}">{{ $group->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="form-row d-none">
                                <!-- Feature Image part start -->
                                <div class="form-group col-md-5">
                                    <label for="feature-img">Feature Image <span style="color: red;">*</span></label>
                                    <input type="file" class="form-control-file" id="feature-img"  name="feature_image" accept="image/*">
                                    <img id="preview-image" src="#" alt="" style="max-width: 300px; width: 100%; height: auto; margin-top: 20px;">
                                </div>
                                <!-- Feature Image part end -->

                                <div class="d-none">
                                <div class="form-group col-md-1">
                                    <label for="is_whole_sale">Whole Sale</label>
                                    <input type="checkbox" class="form-control" id="is_whole_sale" name="is_whole_sale" value="1" checked>
                                </div>
                                <div class="form-group col-md-1">
                                    <label for="is_featured">Featured</label>
                                    <input type="checkbox" class="form-control" id="is_featured" name="is_featured" value="1">
                                </div>
                                <div class="form-group col-md-1">
                                    <label for="is_recent">Recent</label>
                                    <input type="checkbox" class="form-control" id="is_recent" name="is_recent" value="1">
                                </div>
                                <div class="form-group col-md-1">
                                    <label for="is_new_arrival">New Arriv.</label>
                                    <input type="checkbox" class="form-control" id="is_new_arrival" name="is_new_arrival" value="1">
                                </div>
                                <div class="form-group col-md-1">
                                    <label for="is_top_rated">Top Rated</label>
                                    <input type="checkbox" class="form-control" id="is_top_rated" name="is_top_rated" value="1">
                                </div>
                                <div class="form-group col-md-1">
                                    <label for="is_popular">Popular</label>
                                    <input type="checkbox" class="form-control" id="is_popular" name="is_popular" value="1">
                                </div>
                                <div class="form-group col-md-1">
                                    <label for="is_trending">Trending</label>
                                    <input type="checkbox" class="form-control" id="is_trending" name="is_trending" value="1">
                                </div>
                                </div>
                            </div>

                            <div class="form-row d-none">
                                <div class="form-group col-md-5">
                                    <label for="color_id">Select Color</label>
                                    <span class="badge badge-success" style="cursor: pointer;" data-toggle="modal" data-target="#addColorModal">Add New</span>
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
                        <button type="submit" id="addBtn" class="btn btn-secondary" value="Create">Create</button>
                        <div id="loader" style="display: none;">
                            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                            Loading...
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

@include('admin.inc.modal.product_modal')

@endsection

@section('script')

@include('admin.inc.modal.product_modal_script')

<!-- Category Wise Subcategory Start -->
<script>
    $(document).ready(function() {
        $('#category').change(function() {
            var categoryId = $(this).val();
            if (categoryId) {
                $('#subcategory').val('').find('option').hide();
                $('.category-' + categoryId).show();
            } else {
                $('#subcategory').val('').find('option').hide();
                $('#subcategory').find('.subcategory-option').show();
            }
        });
    });
</script>
<!-- Category Wise Subcategory End -->

<!-- Data Table and Select2 -->
<script>
    $(function() {
        $('.select2').select2({
            placeholder: "Select sizes",
            width: '100%'
        });

        $('#long_description, #short_description').summernote({
            height: 100,
        });

        $("#feature-img").change(function(e) {
            var reader = new FileReader();
            reader.onload = function(e) {
                $("#preview-image").attr("src", e.target.result);
            };
            reader.readAsDataURL(this.files[0]);
        });
    });
</script>

<!-- Dynamic Row Script -->
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
    });
</script>

<!-- Create Product Start -->
<script>
    $(document).ready(function() {
        $(document).on('click', '#addBtn', function(e) {
            e.preventDefault();

            $(this).attr('disabled', true);
            $('#loader').show();

            var formData = new FormData($('#createThisForm')[0]);

            $.ajax({
                url: '{{ route("store.product") }}',
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
                },
                complete: function() {
                    $('#loader').hide();
                    $('#addBtn').attr('disabled', false);
                }
            });
        });

        $('#product_code').on('keyup', function() {
            let productCode = $(this).val().trim();

            if (productCode.length >= 2) {
                $.ajax({
                    url: "{{ route('check.product.code') }}",
                    method: "GET",
                    data: { product_code: productCode },
                    success: function(response) {
                        if (response.exists) {
                            $('#productCodeError').text('This product code is already in use.');
                            $('#addBtn').attr('disabled', true);
                        } else {
                            $('#productCodeError').text('');
                            $('#addBtn').attr('disabled', false);
                        }
                    }
                });
            } else {
                $('#productCodeError').text('');
                $('#addBtn').attr('disabled', true);
            }
        });
    });
</script>
<!-- Create Product End -->

@endsection