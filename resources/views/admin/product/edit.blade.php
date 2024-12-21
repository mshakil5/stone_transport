@extends('admin.layouts.admin')

@section('content')

<section class="content pt-3" id="editThisFormContainer">
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
                        <form id="editThisForm">
                            <input type="hidden" name="id" value="{{ $product->id }}">

                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label for="name">Product Name <span style="color: red;">*</span></label>
                                    <input type="text" class="form-control" id="name" name="name" placeholder="Ex. Stylish Running Shoes" value="{{ $product->name }}">
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="product_code">Product Code <span style="color: red;">*</span></label>
                                    <input type="text" class="form-control" id="product_code" name="product_code" placeholder="Ex. PRD-12345" value="{{ $product->product_code }}">
                                    <input type="hidden" id="product_id" value="{{ $product->id }}">
                                    <span id="productCodeError" class="text-danger"></span>
                                </div>
                                <div class="form-group col-md-2 d-none">
                                    <label for="price">Price</label>
                                    <input type="number" class="form-control" id="price" name="price" placeholder="Ex. 1000" value="{{ $product->price }}">
                                </div>
                                <div class="form-group col-md-2 d-none">
                                    <label for="size_ids">Sizes</label>
                                    <span class="badge badge-success" style="cursor: pointer;" data-toggle="modal" data-target="#addSizeModal">Add New</span>
                                    <select class="form-control select2" id="size_ids" name="size_ids[]" multiple="multiple" data-placeholder="Select sizes">
                                        @foreach($sizes as $size)
                                            <option value="{{ $size->id }}" {{ in_array($size->id, $product->sizes->pluck('id')->toArray()) ? 'selected' : '' }}>
                                                {{ $size->size }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group col-md-2 d-none">
                                    <label for="sku">SKU</label>
                                    <input type="number" class="form-control" id="sku" name="sku" placeholder="Ex. 123" value="{{ $product->sku }}">
                                </div>
                            </div>

                            <div class="form-row d-none">
                                <div class="form-group col-md-6">
                                    <label for="short_description">Short Description <span style="color: red;">*</span></label>
                                    <textarea class="form-control" id="short_description" name="short_description">{!! $product->short_description !!}</textarea>
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="long_description">Long Description <span style="color: red;">*</span></label>
                                    <textarea class="form-control" id="long_description" name="long_description">{!! $product->long_description !!}</textarea>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group col-md-4">
                                    <label for="category">Category <span class="badge badge-success" style="cursor: pointer;" data-toggle="modal" data-target="#addCategoryModal">Add New</span></label>
                                    <select class="form-control" id="category" name="category_id">
                                        <option value="">Select Category</option>
                                        @foreach($categories as $category)
                                        <option value="{{ $category->id }}" {{ $category->id == $product->category_id ? 'selected' : '' }}>{{ $category->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group col-md-4 d-none">
                                    <label for="subcategory">Sub Category <span class="badge badge-success" style="cursor: pointer;" data-toggle="modal" data-target="#addSubCategoryModal">Add New</span></label>
                                    <select class="form-control" id="subcategory" name="sub_category_id">
                                        <option value="">Select Sub Category</option>
                                        @foreach($subCategories as $subcategory)
                                        <option value="{{ $subcategory->id }}" class="subcategory-option category-{{ $subcategory->category_id }}" {{ $subcategory->id == $product->sub_category_id ? 'selected' : '' }}>{{ $subcategory->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group col-md-4 d-none">
                                    <label for="brand">Brand <span class="badge badge-success" style="cursor: pointer;" data-toggle="modal" data-target="#addBrandModal">Add New</span></label>
                                    <select class="form-control" id="brand" name="brand_id">
                                        <option value="">Select Brand</option>
                                        @foreach($brands as $brand)
                                        <option value="{{ $brand->id }}" {{ $brand->id == $product->brand_id ? 'selected' : '' }}>{{ $brand->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group col-md-4 d-none">
                                    <label for="model">Model <span class="badge badge-success" style="cursor: pointer;" data-toggle="modal" data-target="#addModelModal">Add New</span></label>
                                    <select class="form-control" id="model" name="product_model_id">
                                        <option value="">Select Model</option>
                                        @foreach($product_models as $model)
                                        <option value="{{ $model->id }}" {{ $model->id == $product->product_model_id ? 'selected' : '' }}>{{ $model->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group col-md-4">
                                    <label for="unit">Unit <span class="badge badge-success" style="cursor: pointer;" data-toggle="modal" data-target="#addUnitModal">Add New</span></label>
                                    <select class="form-control" id="unit" name="unit_id">
                                        <option value="">Select Unit</option>
                                        @foreach($units as $unit)
                                        <option value="{{ $unit->id }}" {{ $unit->id == $product->unit_id ? 'selected' : '' }}>{{ $unit->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group col-md-4">
                                    <label for="group">Group <span class="badge badge-success" style="cursor: pointer;" data-toggle="modal" data-target="#addGroupModal">Add New</span></label>
                                    <select class="form-control" id="group" name="group_id">
                                        <option value="">Select Group</option>
                                        @foreach($groups as $group)
                                        <option value="{{ $group->id }}" {{ $group->id == $product->group_id ? 'selected' : '' }}>{{ $group->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="form-row d-none">
                                <!-- Feature Image part start -->
                                <div class="form-group col-md-5">
                                    <label for="feature-img">Feature Image <span style="color: red;">*</span></label>
                                    <input type="file" class="form-control-file" id="feature-img" name="feature_image" accept="image/*">
                                    
                                    <img id="preview-image" src="{{ asset('/images/products/' . $product->feature_image) }}" alt="Current Image" style="max-width: 300px; width: 100%; height: auto; margin-top: 20px;">
                                </div>
                                <!-- Feature Image part end -->

                                <div class="d-none">
                                <div class="form-group col-md-1">
                                    <label for="is_whole_sale">Whole Sale</label>
                                    <input type="checkbox" class="form-control" id="is_whole_sale" name="is_whole_sale" value="1" {{ $product->is_whole_sale ? 'checked' : '' }}>
                                </div>
                                <div class="form-group col-md-1">
                                    <label for="is_featured">Featured</label>
                                    <input type="checkbox" class="form-control" id="is_featured" name="is_featured" value="1" {{ $product->is_featured ? 'checked' : '' }}>
                                </div>
                                <div class="form-group col-md-1">
                                    <label for="is_recent">Recent</label>
                                    <input type="checkbox" class="form-control" id="is_recent" name="is_recent" value="1" {{ $product->is_recent ? 'checked' : '' }}>
                                </div>
                                <div class="form-group col-md-1">
                                    <label for="is_new_arrival">New Arriv.</label>
                                    <input type="checkbox" class="form-control" id="is_new_arrival" name="is_new_arrival" value="1" {{ $product->is_new_arrival ? 'checked' : '' }}>
                                </div>
                                <div class="form-group col-md-1">
                                    <label for="is_top_rated">Top Rated</label>
                                    <input type="checkbox" class="form-control" id="is_top_rated" name="is_top_rated" value="1" {{ $product->is_top_rated ? 'checked' : '' }}>
                                </div>
                                <div class="form-group col-md-1">
                                    <label for="is_popular">Popular</label>
                                    <input type="checkbox" class="form-control" id="is_popular" name="is_popular" value="1" {{ $product->is_popular ? 'checked' : '' }}>
                                </div>
                                <div class="form-group col-md-1">
                                    <label for="is_trending">Trending</label>
                                    <input type="checkbox" class="form-control" id="is_trending" name="is_trending" value="1" {{ $product->is_trending ? 'checked' : '' }}>
                                </div>
                                </div>
                            </div>

                            <div id="dynamic-rows" class="d-none">
                                @if($product->colors->isEmpty())
                                <div class="form-row dynamic-row">
                                    <div class="form-group col-md-5">
                                        <label for="color_id">Select Color</label>
                                        <select class="form-control" name="color_id[]">
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
                                @endif
                                @foreach($product->colors as $key=> $entry )
                                    <div class="form-row dynamic-row">
                                        <div class="form-group col-md-5">
                                            <label for="color_id">Select Color</label>
                                            <select class="form-control" name="color_id[]">
                                                <option value="">Choose Color</option>
                                                @foreach($colors as $color)
                                                    <option value="{{ $color->id }}" style="background-color: {{ $color->color_code }};" {{ $color->id == $entry->color_id ? 'selected' : '' }}>
                                                        {{ $color->color }} ({{ $color->color_code }})
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="form-group col-md-5">
                                            <label for="image">Select Image</label>
                                            <input type="file" class="form-control" name="image[]">
                                            @if($entry->image)
                                                <img src="{{ asset($entry->image) }}" alt="Image" style="max-width: 100px; margin-top: 10px;">
                                            @endif
                                        </div>
                                        <div class="form-group col-md-1">
                                            <label>Action</label>
                                            @if($key == 0)
                                            <button type="button" class="btn btn-success add-row"><i class="fas fa-plus"></i></button>
                                            @else
                                            <button type="button" class="btn btn-danger remove-row"><i class="fas fa-minus"></i></button>
                                            @endif                                  
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </form>
                        <button type="submit" id="addBtn" class="btn btn-secondary">Update</button>
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

<!-- Category Wise Subcategory and Product Code Check Start -->
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

        $('#product_code').on('keyup', function() {
            let productCode = $(this).val().trim();
            let productId = $('#product_id').val();

            if (productCode.length >= 2) {
                $.ajax({
                    url: "{{ route('check.product.code') }}",
                    method: "GET",
                    data: { product_code: productCode, product_id: productId },
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

        $(document).on('click', '.add-row', function() {
            let newRow = `
            <div class="form-row dynamic-row">
                <div class="form-group col-md-5">
                    <label for="color_id">Select Color</label>
                    <select class="form-control" name="color_id[]">
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

<!-- Update Product -->
<script>
    $(document).ready(function() {

        $(document).on('click', '#addBtn', function(e) {
            e.preventDefault();

            $(this).attr('disabled', true);
            $('#loader').show();

            var formData = new FormData($('#editThisForm')[0]);

            formData.forEach(function(value, key) {
                console.log(key + ": " + value);
            });


            $.ajax({
                url: '{{ route("update.product") }}',
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
    });
</script>

@endsection