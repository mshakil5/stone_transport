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
            <div class="col-md-8">
                <div class="card card-secondary">
                    <div class="card-header">
                        <h3 class="card-title" id="cardTitle">Add New Related Product</h3>
                    </div>
                    <div class="card-body">
                        <div class="ermsg" style="color: red;"></div>
                        <form id="createThisForm">
                            <input type="hidden" class="form-control" id="codeid" name="codeid">
                            <div class="form-row">
                                <div id="product_display"></div>
                                <div class="form-group col-md-12" id="product_section">
                                    <label for="product_id">Select Product <span style="color: red;">*</span></label>
                                    <select class="form-control select2" id="product_id" name="product_id">
                                        @foreach($products as $product)
                                        <option value="{{ $product->id }}">{{ $product->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                 <div class="form-group col-md-12">
                                    <label for="related_product_ids">Products <span style="color: red;">*</span></label>
                                    <select class="form-control select2" id="related_product_ids" name="related_product_ids[]" multiple="multiple" data-placeholder="Select products">
                                        @foreach($products as $product)
                                        <option value="{{ $product->id }}">{{ $product->name }}</option>
                                        @endforeach
                                    </select>
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
                        <h3 class="card-title">Bundle Products</h3>
                    </div>
                    <div class="card-body">
                        <table id="bundleProductsTable" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Product</th>
                                    <th>Related Products</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($relatedProducts as $relatedProduct)
                                <tr>
                                    <td>{{ $relatedProduct->id }}</td>
                                    <td>{{ $relatedProduct->product->name }}</td>
                                    <td>
                                        @foreach(json_decode($relatedProduct->related_product_ids) as $productId)
                                            @php
                                                $product = $products->where('id', $productId)->first();
                                            @endphp
                                            @if($product)
                                                {{ $product->name }}{{ !$loop->last ? ', ' : '' }}
                                            @else
                                                Product not found{{ !$loop->last ? ', ' : '' }}
                                            @endif
                                        @endforeach
                                    </td>
                                    <td>
                                        <a class="editBtn" rid="{{ $relatedProduct->id }}">
                                            <i class="fa fa-edit" style="color: #2196f3; font-size:16px;"></i>
                                        </a>
                                        <a id="deleteBtn" rid="{{ $relatedProduct->id }}">
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

@endsection

@section('script')

<script>
    var products = @json($products); 
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
            $('#related_product_ids').val(null).trigger('change');
        }

        $.ajaxSetup({
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
        });

            $('#addBtn').click(function() {

                if($(this).val() == 'Create') {
                    var formData = new FormData($('#createThisForm')[0]);

                    // for (let [key, value] of formData.entries()) {
                    //     console.log(key, value);
                    // }
                    
                    $.ajax({
                        url: "{{ route('relatedproduct.store') }}",
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

                    formData.append("codeid", $("#codeid").val());
                    
                    $.ajax({
                        url: "{{URL::to('/admin/related-product-update')}}",
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
                            // console.log(response);
                        },
                        error: function(xhr, status, error) {
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
                info_url = '{{URL::to('/admin/related-product')}}' + '/'+codeid+'/edit';
                $.get(info_url,{},function(d){
                    populateForm(d);
                    pagetop();
                });
            });

            $("#contentContainer").on('click','.deleteBtn', function(){
                if(!confirm('Sure?')) return;
                codeid = $(this).attr('rid');
                info_url = '{{URL::to('/admin/related-product')}}' + '/'+codeid;
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

                 if (data.related_product_ids) {
                    var selectedProductIds = JSON.parse(data.related_product_ids);
                    $("#related_product_ids").val(selectedProductIds).trigger('change');
                }

                $("#codeid").val(data.id);
                $("#addBtn").val('Update');
                $("#addBtn").html('Update');
                $("#addThisFormContainer").show(300);
                $("#newBtn").hide(100);

                $("#product_section").hide(300);

                var productName = data.product.name;
                $("#product_display").html('<p>Product: ' + productName + '</p>').show(300);
            }
        
    });
</script>

@endsection