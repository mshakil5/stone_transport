@extends('admin.layouts.admin')

@section('content')

<!-- Main content -->
<section class="content" id="newBtnSection">
    <div class="container-fluid">
        <div class="row">
            <div class="col-2 my-2">
                <a href="{{ route('allproduct') }}" class="btn btn-secondary">
                    <i class="fa fa-arrow-left"></i> Back
                </a>
                <button type="button" class="btn btn-secondary my-3" id="newBtn">Add New Price</button>
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
                        <h3 class="card-title" id="cardTitle">Add New Price</h3>
                    </div>
                    <div class="card-body">
                        <div class="ermsg"></div>
                        <form id="createThisForm">
                            @csrf
                            <input type="hidden" class="form-control" id="priceId" name="priceId">
                            <input type="hidden" class="form-control" id="productId" name="productId" value="{{ $product->id }}">
                            <div class="row">
                                @php
                                    $sellingPrice = $product->stockhistory()
                                        ->where('available_qty', '>', 0)
                                        ->orderBy('id', 'asc')
                                        ->value('selling_price');
                                @endphp
                                @if($sellingPrice)
                                    <div class="col-sm-12">
                                        <div class="card text-center">
                                            <div class="card-body">
                                                <h5 class="card-title d-inline">Per Unit Selling Price of {{ $product->name }}: {{ number_format($sellingPrice, 2) }}</h5>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label>Min Quantity <span style="color: red;">*</span></label>
                                        <input type="number" class="form-control" id="min_quantity" name="min_quantity" placeholder="Enter minimum quantity" required>
                                    </div>
                                </div>
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label>Max Quantity <span style="color: red;">*</span></label>
                                        <input type="number" class="form-control" id="max_quantity" name="max_quantity" placeholder="Enter maximum quantity" required>
                                    </div>
                                </div>
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label>Price <span style="color: red;">*</span></label>
                                        <input type="text" class="form-control" id="price" name="price" placeholder="Enter price" required>
                                    </div>
                                </div>
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label>Status</label>
                                        <select class="form-control" id="status" name="status">
                                            <option value="1">Active</option>
                                            <option value="0">Inactive</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="card-footer">
                        <button type="button" id="addBtn" class="btn btn-secondary" value="Create">Create</button>
                        <button type="button" id="FormCloseBtn" class="btn btn-default">Cancel</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="content" id="contentContainer">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card card-secondary">
                    <div class="card-header">
                        <h3 class="card-title">All Prices for {{ $product->name }}</h3>
                    </div>
                    <div class="card-body">
                        <table id="example1" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Sl</th>
                                    <th>Min Quantity</th>
                                    <th>Max Quantity</th>
                                    <th>Price</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($prices as $key => $price)
                                <tr>
                                    <td>{{ $key + 1 }}</td>
                                    <td>{{ $price->min_quantity }}</td>
                                    <td>{{ $price->max_quantity }}</td>
                                    <td>{{ number_format($price->price, 2) }}</td>
                                    <td>
                                        <div class="custom-control custom-switch">
                                            <input type="checkbox" class="custom-control-input toggle-status" id="customSwitchStatus{{ $price->id }}" data-id="{{ $price->id }}" {{ $price->status == 1 ? 'checked' : '' }}>
                                            <label class="custom-control-label" for="customSwitchStatus{{ $price->id }}"></label>
                                        </div>
                                    </td>
                                    <td>
                                        <a id="EditBtn" rid="{{ $price->id }}">
                                            <i class="fa fa-edit" style="color: #2196f3; font-size:16px;"></i>
                                        </a>
                                        <a id="deleteBtn" rid="{{ $price->id }}">
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
    $(function () {
        $("#example1").DataTable({
            "responsive": true, "lengthChange": false, "autoWidth": false,
            "buttons": ["copy", "csv", "excel", "pdf", "print"]
        }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
    });

    $(document).ready(function() {
        $("#addThisFormContainer").hide();
        $("#newBtn").click(function(){
            clearform();
            $("#newBtn").hide(100);
            $("#addThisFormContainer").show(300);
        });
        $("#FormCloseBtn").click(function(){
            $("#addThisFormContainer").hide(200);
            $("#newBtn").show(100);
            clearform();
        });

        var url = "{{URL::to('/admin/product-price')}}";
        var upurl = "{{URL::to('/admin/product-price-update')}}";

        $("#addBtn").click(function(){
            if($(this).val() == 'Create') {
                var form_data = new FormData();
                form_data.append("product_id", $("#productId").val());
                form_data.append("min_quantity", $("#min_quantity").val());
                form_data.append("max_quantity", $("#max_quantity").val());
                form_data.append("price", $("#price").val());
                form_data.append("status", $("#status").val());

                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                $.ajax({
                    url: url,
                    method: "POST",
                    contentType: false,
                    processData: false,
                    data: form_data,
                    success: function (d) {
                        if (d.status == 303) {
                            $(".ermsg").html(d.message);
                        } else if(d.status == 300) {
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
                        }
                    },
                    error: function(xhr, status, error) {
                        swal({
                            text: xhr.responseText,
                            icon: "error",
                            button: {
                                text: "OK",
                                className: "swal-button--confirm"
                            }
                        })
                        console.error(xhr.responseText);
                    }
                });
            }

            if($(this).val() == 'Update') {
                var form_data = new FormData();
                form_data.append("product_id", $("#productId").val());
                form_data.append("min_quantity", $("#min_quantity").val());
                form_data.append("max_quantity", $("#max_quantity").val());
                form_data.append("price", $("#price").val());
                form_data.append("status", $("#status").val());
                form_data.append("priceId", $("#priceId").val());

                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                $.ajax({
                    url: upurl,
                    type: "POST",
                    dataType: 'json',
                    contentType: false,
                    processData: false,
                    data: form_data,
                    success: function(d) {
                        if (d.status == 303) {
                            $(".ermsg").html(d.message);
                        } else if(d.status == 300) {
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
                        }
                    },
                    error: function(xhr, status, error) {
                        swal({
                            text: xhr.responseText,
                            icon: "error",
                            button: {
                                text: "OK",
                                className: "swal-button--confirm"
                            }
                        })
                        console.error(xhr.responseText);
                    }
                });
            }
        });

        $("#contentContainer").on('click', '#EditBtn', function(){
            $("#cardTitle").text('Update Price');
            var priceId = $(this).attr('rid');
            var info_url = url + '/' + priceId + '/edit';
            $.get(info_url, {}, function(d){
                populateForm(d);
            });
        });

        $("#contentContainer").on('click', '#deleteBtn', function(){
            if(!confirm('Are you sure?')) return;
            var priceId = $(this).attr('rid');
            var info_url = url + '/' + priceId;
            $.ajax({
                url: info_url,
                method: "get",
                success: function(d){
                    if(d.success) {
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
                    }
                },
                error: function(d){
                    console.error(d);
                }
            });
        });

        function populateForm(data){
            $("#min_quantity").val(data.min_quantity);
            $("#max_quantity").val(data.max_quantity);
            $("#price").val(data.price);
            $("#status").val(data.status);
            $("#priceId").val(data.id);
            $("#addBtn").val('Update');
            $("#addBtn").html('Update');
            $("#addThisFormContainer").show(300);
            $("#newBtn").hide(100);
        }

        function clearform(){
            $('#createThisForm')[0].reset();
            $("#addBtn").val('Create');
            $("#addBtn").html('Create');
            $("#cardTitle").text('Add New Price');
        }

        $(document).on('change', '.toggle-status', function() {
            var priceId = $(this).data('id');
            var status = $(this).is(':checked') ? 1 : 0;

            $.ajax({
                url: "{{ route('product-price.update-status') }}",
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    price_id: priceId,
                    status: status
                },
                success: function(response) {
                    if(response.status === 200) {
                        swal({
                            text: response.message,
                            icon: "success",
                            button: {
                                text: "OK",
                                className: "swal-button--confirm"
                            }
                        })
                    } else {
                        swal({
                            text: response.message,
                            icon: "error",
                            button: {
                                text: "OK",
                                className: "swal-button--confirm"
                            }
                        });
                    }
                },
                error: function(xhr, status, error) {
                    console.error(xhr.responseText);
                }
            });
        });

    });
</script>

@endsection