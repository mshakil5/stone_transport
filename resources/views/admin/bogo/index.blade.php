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
                        <h3 class="card-title" id="cardTitle">Add New Buy One Get</h3>
                    </div>
                    <div class="card-body">
                        <div class="ermsg" style="color: red;"></div>
                        <form id="createThisForm">
                            <input type="hidden" class="form-control" id="codeid" name="codeid">
                            <div class="form-row">
                                <div class="col-md-12" id="product_display"></div>
                                <div class="form-group col-md-12" id="product_section">
                                    <label for="product_id">Select Product <span style="color: red;">*</span></label>
                                    <select class="form-control select2" id="product_id" name="product_id">
                                        @foreach($products as $product)
                                        <option value="{{ $product->id }}" data-price="{{ $product->price }}">{{ $product->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="price">Price <span style="color: red;">*</span></label>
                                    <input type="number" class="form-control" id="price" name="price" placeholder="Enter product price">
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="quantity">Quantity <span style="color: red;">*</span></label>
                                    <input type="number" class="form-control" id="quantity" name="quantity" placeholder="Enter quantity">
                                </div>
                                 <div class="form-group col-md-12">
                                    <label for="get_product_ids">Products<span style="color: red;">*</span></label>
                                    <select class="form-control select2" id="get_product_ids" name="get_product_ids[]" multiple="multiple" data-placeholder="Select products">
                                        @foreach($products as $product)
                                        <option value="{{ $product->id }}">{{ $product->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group col-md-6">
                                    <label for="short_description">Short Description</label>
                                    <textarea class="form-control" id="short_description" name="short_description" rows="3" placeholder="Enter bundle product short description"></textarea>
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="long_description">Long Description</label>
                                    <textarea class="form-control" id="long_description" name="long_description" rows="3" placeholder="Enter bundle product long description"></textarea>
                                </div>

                                <div class="form-group col-md-6">
                                    <label for="feature-img">Feature Image<span style="color: red;">*</span></label>
                                    <input type="file" class="form-control-file" id="feature-img" accept="image/*">
                                    <img id="preview-image" src="#" alt="" style="max-width: 300px; width: 100%; height: auto; margin-top: 20px;">
                                </div>

                                <div class="form-group col-md-6">
                                    <label>Slider Images</label>
                                    <div id="dynamicImages">
                                        <div class="image-input-wrapper">
                                            <img src="#" alt="Choose image" id="previewImage1" style="width: 150px; height: auto;">
                                            <div class="image-input-icon">
                                                <i class="fas fa-times-circle remove-image" title="Remove this image"></i>
                                            </div>
                                            <input type="file" class="form-control-file" id="imageUpload1" onchange="loadFile(event)" multiple accept="image/*">
                                        </div>
                                    </div>
                                    <button type="button" class="btn btn-secondary btn-sm" onclick="addMoreImages()">+ Add More</button>
                                </div> 

                            </div>
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
                        <h3 class="card-title">Buy One Get One Products</h3>
                    </div>
                    <div class="card-body">
                        <table id="bundleProductsTable" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Product</th>
                                    <th>Price</th>
                                    <th>Quantity</th>
                                    <th>Get Products</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($bogoProducts as $bogoProduct)
                                <tr>
                                    <td>{{ $bogoProduct->id }}</td>
                                    <td>{{ $bogoProduct->product->name }}</td>
                                    <td>{{ $bogoProduct->price }}</td>
                                    <td>{{ $bogoProduct->quantity }}</td>
                                    <td>
                                        @php
                                            $associatedProducts = json_decode($bogoProduct->get_product_ids, true);
                                        @endphp
                                        @if($associatedProducts)
                                            @foreach($associatedProducts as $productId)
                                                @php
                                                    $product = $allProducts->where('id', $productId)->first();
                                                @endphp
                                                {{ $product->name }}
                                                {{ !$loop->last ? ', ' : '' }}
                                            @endforeach
                                        @else
                                            No associated products
                                        @endif
                                    </td>
                                    <td>
                                        <a class="editBtn" rid="{{ $bogoProduct->id }}">
                                            <i class="fa fa-edit" style="color: #2196f3; font-size:16px;"></i>
                                        </a>
                                        <a class="deleteBtn" rid="{{ $bogoProduct->id }}">
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

<style>
    #dynamicImages {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
    }

    .image-input-wrapper {
        flex: 0 0 auto;
        display: inline-block; 
        vertical-align: top;
        text-align: center;
        width: calc(25% - 10px);
        margin-bottom: 10px;
        position: relative;
    }

    .image-input-wrapper img {
        max-width: 100%;
        height: auto;
    }

    .image-input-icon {
        position: absolute;
        top: 5px;
        right: 5px;
        z-index: 10;
        background-color: rgba(255, 255, 255, 0.8);
        border-radius: 50%;
        padding: 5px;
        cursor: pointer;
    }

    .image-input-icon i {
        color: red;
    }

</style>

@endsection

@section('script')

<script>
    var products = @json($products); 
</script>

<script>
    $(document).ready(function() {
        $('#long_description, #short_description').summernote({
            height: 100,
        });
    });
</script>

<script>
    $(document).ready(function(){
        $("#feature-img").change(function(e){
            var reader = new FileReader();
            reader.onload = function(e){
                $("#preview-image").attr("src", e.target.result);
            };
            reader.readAsDataURL(this.files[0]);
        });
    });
</script>

<script>
    $(document).ready(function() {
        $('#product_id').change(function() {
            var selectedOption = $(this).find('option:selected');
            var price = selectedOption.data('price');
            $('#price').val(price ? price : '');
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
            $("#product_display").hide(200);
            $("#newBtn").show(100);
            $("#product_section").show(100);
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
            placeholder: "Select products",
            width: '100%'
        });

        function clearForm(){
            $('#createThisForm')[0].reset();
            $("#addBtn").val('Create').text('Create');
            $("#cardTitle").text('Add new data');
            $('#get_product_ids').val(null).trigger('change');
            $('#preview-image').attr('src', '#');
            $('#dynamicImages').empty();
            $('#feature-img').val('');
            $('#imageUpload1').val('');
            $("#long_description").summernote('code', '');
            $("#short_description").summernote('code', '');
        }

        $.ajaxSetup({
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
        });

            $('#addBtn').click(function() {

                if($(this).val() == 'Create') {
                    var formData = new FormData($('#createThisForm')[0]);

                    var featureImgInput = document.getElementById('feature-img');
                    if(featureImgInput.files && featureImgInput.files[0]) {
                        formData.append("feature_image", featureImgInput.files[0]);
                    }

                prepareImageData(formData);

                function prepareImageData(formData) {
                        $(".image-input-wrapper").each(function(index) {
                            var imageInputs = $(this).find('input[type=file]');
                            imageInputs.each(function() {
                                var files = this.files; 
                                if (files && files.length > 0) {
                                    Array.from(files).forEach(file => {
                                        formData.append("images[]", file);
                                    });
                                }
                            });
                        });
                    }

                    // for (let [key, value] of formData.entries()) {
                    //     console.log(key, value);
                    // }
                    
                    $.ajax({
                        url: "{{ route('bogoproduct.store') }}",
                        method: "POST",
                        contentType: false,
                        processData: false,
                        data: formData,
                        success: function (response) {
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
                            // console.log(response);
                        },
                         error: function(xhr, status, error) {
                            console.error(xhr.responseText);
                            $('.ermsg').empty();
                            if (xhr.responseJSON && xhr.responseJSON.errors) {
                                var errors = xhr.responseJSON.errors;
                                $.each(errors, function(key, error) {
                                    $('.ermsg').append('<p>' + error + '</p>');
                                });
                            }
                        }
                    });
                }

                if($(this).val() == 'Update') {
                    var formData = new FormData($('#createThisForm')[0]);

                    var featureImgInput = document.getElementById('feature-img');
                    if(featureImgInput.files && featureImgInput.files[0]) {
                        formData.append("feature_image", featureImgInput.files[0]);
                    }

                    $("input[name='existing_images[]']").each(function() {
                        formData.append('existing_images[]', $(this).val());
                    });

                prepareImageData(formData);

                function prepareImageData(formData) {
                        $(".image-input-wrapper").each(function(index) {
                            var imageInputs = $(this).find('input[type=file]');
                            imageInputs.each(function() {
                                var files = this.files; 
                                if (files && files.length > 0) {
                                    Array.from(files).forEach(file => {
                                        formData.append("images[]", file);
                                    });
                                }
                            });
                        });
                    }

                    formData.append("codeid", $("#codeid").val());

                    // for (let [key, value] of formData.entries()) {
                    //     console.log(key, value);
                    // }
                    
                    $.ajax({
                        url: "{{URL::to('/admin/bogo-product-update')}}",
                        method: "POST",
                        contentType: false,
                        processData: false,
                        data: formData,
                        success: function (response) {
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
                            console.error(xhr.responseText);
                            $('.ermsg').empty();
                            if (xhr.responseJSON && xhr.responseJSON.errors) {
                                var errors = xhr.responseJSON.errors;
                                $.each(errors, function(key, error) {
                                    $('.ermsg').append('<p>' + error + '</p>');
                                });
                            }
                        }
                    });
                }

            });
        
            $(".editBtn").on("click", function(){
                $("#cardTitle").text('Update this data');
                codeid = $(this).attr('rid');
                info_url = '{{URL::to('/admin/bogo-product')}}' + '/'+codeid+'/edit';
                $.get(info_url,{},function(d){
                    // console.log(d);
                    populateForm(d);
                    pagetop();
                });
            });

            $("#contentContainer").on('click','.deleteBtn', function(){
                if(!confirm('Sure?')) return;
                codeid = $(this).attr('rid');
                info_url = '{{URL::to('/admin/bogo-product')}}' + '/'+codeid;
                $.ajax({
                    url:info_url,
                    method: "GET",
                    type: "DELETE",
                    data:{
                    },
                    success: function(d){
                            swal({
                                text: "Deleted",
                                icon: "success",
                                button: {
                                    text: "OK",
                                    className: "swal-button--confirm"
                                }
                            }).then(() => {
                                location.reload();
                            });
                        },
                        error:function(d){
                            // console.log(d);
                        }
                });
            });

            function populateForm(data){
                // console.log(data);

                 if (data.get_product_ids) {
                    var selectedProductIds = JSON.parse(data.get_product_ids);
                    $("#get_product_ids").val(selectedProductIds).trigger('change');
                }

                $("#short_description").val(data.short_description);
                $('#short_description').summernote('code', data.short_description);

                $("#long_description").val(data.long_description);
                $('#long_description').summernote('code', data.long_description);

                var featureImagePreview = document.getElementById('preview-image');
                if (data.feature_image) { 
                    featureImagePreview.src = '/images/buy_one_get_one/' + data.feature_image; 
                } else {
                    featureImagePreview.src = "#";
                }

                if (data.images && data.images.length > 0) {
                    var imagesHTML = '';
                    data.images.forEach(function(image) {
                        var imagePath = '/images/buy_one_to_one_product_images/' + image.image;
                        imagesHTML += '<div class="image-input-wrapper">';
                        imagesHTML += '<img src="' + imagePath + '" alt="Product Image" style="width: 150px; height: 150px; object-fit: cover;">';
                        imagesHTML += '<input type="hidden" name="existing_images[]" value="' + image.image + '">';
                        imagesHTML += '<div class="image-input-icon"><i class="fas fa-times-circle remove-image" title="Remove this image"></i></div>';
                        imagesHTML += '</div>';
                    });
                    $('#dynamicImages').html(imagesHTML);

                    $('#dynamicImages').on('click', '.remove-image', function(e) {
                        e.preventDefault();
                        $(this).closest('.image-input-wrapper').remove();
                    });
                }

                $("#codeid").val(data.id);
                $("#addBtn").val('Update');
                $("#addBtn").html('Update');
                $("#addThisFormContainer").show(300);
                $("#newBtn").hide(100);
                $("#product_section").hide(300);
                $("#price").val(data.price);
                $("#quantity").val(data.quantity);
                var productName = data.product.name;
                $("#product_display").html('<p>Product: <b>' + productName + '</b></p>').show(300);
            }
        
    });
</script>

<script>
    let imagesCount = 1;

    function loadFile(event) {
        const output = document.getElementById('previewImage' + event.target.id.split('imageUpload')[1]);
        output.src = URL.createObjectURL(event.target.files[0]);
        output.onload = () => URL.revokeObjectURL(output.src);
    }

    function addMoreImages() {
        imagesCount++;
        const newInputDiv = document.createElement('div');
        newInputDiv.classList.add('image-input-wrapper');

        newInputDiv.innerHTML = `
            <img src="#" alt="Choose image" id="previewImage${imagesCount}" style="width: 150px; height: 150px; object-fit: cover;">
            <div class="image-input-icon">
                <i class="fas fa-times-circle remove-image" title="Remove this image"></i>
            </div>
            <input type="file" class="form-control-file" id="imageUpload${imagesCount}" onchange="loadFile(event)" multiple accept="image/*">`;

        document.getElementById('dynamicImages').appendChild(newInputDiv);

        newInputDiv.querySelector('.remove-image').addEventListener('click', function() {
            newInputDiv.remove();
        });
    }

</script>

@endsection