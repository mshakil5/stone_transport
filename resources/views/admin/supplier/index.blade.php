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
                        <h3 class="card-title" id="cardTitle">Add new data</h3>
                    </div>
                    <div class="card-body">
                        <div class="ermsg"></div>
                        <form id="createThisForm">
                            @csrf
                            <input type="hidden" class="form-control" id="codeid" name="codeid">
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Name <span style="color: red;">*</span></label>
                                        <input type="text" class="form-control" id="name" name="name" placeholder="Enter name">
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Supplier ID <span style="color: red;">*</span></label>
                                        <input type="number" class="form-control" id="id_number" name="id_number" placeholder="Enter code">
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Email</label>
                                        <input type="email" class="form-control" id="email" name="email" placeholder="Enter email">
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Phone</label>
                                        <input type="number" class="form-control" id="phone" name="phone" placeholder="Enter phone">
                                    </div>
                                </div>
                                <div class="col-sm-6 d-none">
                                    <div class="form-group">
                                        <label>Password</label>
                                        <input type="password" class="form-control" id="password" name="password" placeholder="Enter password" value="123456">
                                    </div>
                                </div>
                                <div class="col-sm-6 d-none">
                                    <div class="form-group">
                                        <label>Confirm Password</label>
                                        <input type="password" class="form-control" id="confirm_password" name="confirm_password" placeholder="Enter password" value="123456">
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Vat Reg</label>
                                        <input type="number" class="form-control" id="vat_reg" name="vat_reg" placeholder="Enter vat reg">
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Contract Date</label>
                                        <input type="date" class="form-control" id="contract_date" name="contract_date" placeholder="Enter contract date">
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Address</label>
                                        <textarea class="form-control" id="address" name="address" rows="3" placeholder="Enter address"></textarea>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Company</label>
                                        <textarea class="form-control" id="company" name="company" rows="3" placeholder="Enter company"></textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-10">
                                    <div class="form-group">
                                        <label for="feature-img">Supplier Image</label>
                                        <input type="file" class="form-control-file" id="image" accept="image/*">
                                        <img id="preview-image" src="#" alt="" style="max-width: 300px; width: 100%; height: auto; margin-top: 20px;">
                                    </div>
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

<div class="modal fade" id="payModal" tabindex="-1" role="dialog" aria-labelledby="payModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="payModalLabel">Supplier Payment Form</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="payForm">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="paymentAmount">Payment Amount <span style="color: red;">*</span></label>
                        <input type="number" class="form-control" id="paymentAmount" name="paymentAmount" placeholder="Enter payment amount">
                    </div>

                    <div class="form-group">
                        <label for="document">Document</label>
                        <input type="file" class="form-control-file" id="document" name="document">
                    </div>

                    <div class="form-group">
                        <label for="payment_type">Payment Type <span style="color: red;">*</span></label>
                        <select name="payment_type" id="payment_type" class="form-control">
                            <option value="Cash">Cash</option>
                            <option value="Bank">Bank</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="paymentNote">Payment Note</label>
                        <textarea class="form-control" id="paymentNote" name="paymentNote" rows="3" placeholder="Enter payment note"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning">Pay</button>
                </div>
            </form>
        </div>
    </div>
</div>

<section class="content" id="contentContainer">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card card-secondary">
                    <div class="card-header">
                        <h3 class="card-title">All Data</h3>
                    </div>
                    <div class="card-body">
                        <table id="example1" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Sl</th>
                                    <th>Supplier ID</th>
                                    <th>Name/Email/Number</th>
                                    {{-- <th>Stock</th> --}}
                                    <th>Balance</th>
                                    <th>Transactions</th>
                                    <th>Orders</th>
                                    <th>Active</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($data as $key => $data)
                                <tr>
                                    <td>{{ $key + 1 }}</td>
                                    <td>{{ $data->id_number }}</td>
                                    <td>{{ $data->name }} <br> {{ $data->email }} <br>{{ $data->phone }}</td>
                                    {{-- <td>
                                        <button class="btn btn-info" type="button" onclick="location.href='{{ route('supplier.stocks', ['id' => $data->id]) }}'">Stocks</button>
                                    </td> --}}
                                    <td>
                                        <div class="align-items-center">
                                            @if ($data->supplier_transaction_sum_at_amount-$data->total_decreament > 0)
                                            <span class="btn btn-sm btn-danger">£ {{ number_format($data->supplier_transaction_sum_at_amount - $data->total_decreament, 2) }}</span>
                                            <button class="btn btn-sm btn-warning pay-btn" data-id="{{ $data->id }}" data-supplier-id="{{ $data->id }}">Pay</button>
                                            @endif
                                        </div>
                                        <input type="hidden" id="supplierId" name="supplierId">
                                    </td>
                                    <td>
                                        <a href="{{ route('supplier.transactions', ['supplierId' => $data->id]) }}" class="btn btn-info">
                                            Transactions
                                        </a>
                                    </td>
                                    <td>
                                        {{-- @if ($data->order_details_count > 0)
                                            <a href="{{ route('supplier.orders', ['supplierId' => $data->id]) }}" class="btn btn-info">
                                        Orders ({{ $data->order_details_count }})
                                        </a>
                                        @else
                                        0
                                        @endif --}}

                                        @if ($data->purchase_count > 0)
                                        <a href="{{ route('supplier.purchase', ['supplierId' => $data->id]) }}" class="btn btn-info">
                                            Purchase ({{ $data->purchase_count }})
                                        </a>
                                        @else
                                        0
                                        @endif
                                    </td>
                                    <td>
                                        <div class="custom-control custom-switch">
                                            <input type="checkbox" class="custom-control-input toggle-status" id="customSwitchStatus{{ $data->id }}" data-id="{{ $data->id }}" {{ $data->status == 1 ? 'checked' : '' }}>
                                            <label class="custom-control-label" for="customSwitchStatus{{ $data->id }}"></label>
                                        </div>
                                    </td>
                                    <td>

                                        <a class="btn btn-app" href="{{route('supplier.email', $data->id)}}">
                                            <i class="fas fa-envelope"></i> Email
                                        </a>
                                        <a class="btn btn-app" id="EditBtn" rid="{{ $data->id }}">
                                            <i class="fas fa-edit"></i> Edit
                                        </a>

                                        <a class="btn btn-app" id="deleteBtn" rid="{{ $data->id }}">
                                            <i class="fa fa-trash-o" style="color: red; font-size:16px;"></i>Delete
                                        </a>

                                        {{-- <a href="#"><i class="fa fa-envelope-o" style="color: #747678;font-size:16px;"></i></a> --}}
                                        {{-- <a id="EditBtn" rid="{{ $data->id }}">
                                        <i class="fa fa-edit" style="color: #2196f3; font-size:16px;"></i>
                                        </a>
                                        <a id="deleteBtn" rid="{{ $data->id }}">
                                            <i class="fa fa-trash-o" style="color: red; font-size:16px;"></i>
                                        </a> --}}
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
    $(function() {
        $("#example1").DataTable({
            "responsive": true,
            "lengthChange": false,
            "autoWidth": false,
            "buttons": ["copy", "csv", "excel", "pdf", "print"]
        }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
    });
</script>

<script>
    $(document).ready(function() {
        $('.toggle-status').change(function() {
            var isChecked = $(this).is(':checked');
            var supplierId = $(this).data('id');

            $.ajax({
                url: '/admin/toggle-supplier-status',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    id: supplierId,
                    status: isChecked ? 1 : 0
                },
                success: function(response) {
                    swal({
                        text: "Supplier status updated successfully",
                        icon: "success",
                    });
                },
                error: function(xhr) {
                    console.error(xhr.responseText);
                    swal({
                        text: "There was an error updating the supplier status.",
                        icon: "error",
                    });
                }
            });
        });
    });
</script>

<script>
    $(document).ready(function() {
        $("#addThisFormContainer").hide();
        $("#newBtn").click(function() {
            clearform();
            $("#newBtn").hide(100);
            $("#addThisFormContainer").show(300);

        });
        $("#FormCloseBtn").click(function() {
            $("#addThisFormContainer").hide(200);
            $("#newBtn").show(100);
            clearform();
        });

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        //
        var url = "{{URL::to('/admin/supplier')}}";
        var upurl = "{{URL::to('/admin/supplier-update')}}";

        $("#addBtn").click(function() {

            //create
            if ($(this).val() == 'Create') {
                var form_data = new FormData();
                form_data.append("id_number", $("#id_number").val());
                form_data.append("name", $("#name").val());
                form_data.append("email", $("#email").val());
                form_data.append("phone", $("#phone").val());
                form_data.append("vat_reg", $("#vat_reg").val());
                form_data.append("address", $("#address").val());
                form_data.append("company", $("#company").val());
                form_data.append("contract_date", $("#contract_date").val());
                form_data.append("password", $("#password").val());
                form_data.append("confirm_password", $("#confirm_password").val());

                var featureImgInput = document.getElementById('image');
                if (featureImgInput.files && featureImgInput.files[0]) {
                    form_data.append("image", featureImgInput.files[0]);
                }

                $.ajax({
                    url: url,
                    method: "POST",
                    contentType: false,
                    processData: false,
                    data: form_data,
                    success: function(d) {
                        if (d.status == 303) {
                            $(".ermsg").html(d.message);
                        } else if (d.status == 300) {
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
                        console.error(xhr.responseText);
                    }
                });
            }
            //create  end

            //Update
            if ($(this).val() == 'Update') {
                var form_data = new FormData();
                form_data.append("id_number", $("#id_number").val());
                form_data.append("name", $("#name").val());
                form_data.append("email", $("#email").val());
                form_data.append("phone", $("#phone").val());
                form_data.append("vat_reg", $("#vat_reg").val());
                form_data.append("address", $("#address").val());
                form_data.append("company", $("#company").val());
                form_data.append("contract_date", $("#contract_date").val());
                form_data.append("password", $("#password").val());
                form_data.append("confirm_password", $("#confirm_password").val());

                var featureImgInput = document.getElementById('image');
                if (featureImgInput.files && featureImgInput.files[0]) {
                    form_data.append("image", featureImgInput.files[0]);
                }

                form_data.append("codeid", $("#codeid").val());

                $.ajax({
                    url: upurl,
                    type: "POST",
                    dataType: 'json',
                    contentType: false,
                    processData: false,
                    data: form_data,
                    success: function(d) {
                        //   console.log(d);
                        if (d.status == 303) {
                            $(".ermsg").html(d.message);
                            pagetop();
                        } else if (d.status == 300) {
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
                        console.error(xhr.responseText);
                    }
                });
            }
            //Update  end
        });
        //Edit
        $("#contentContainer").on('click', '#EditBtn', function() {
            $("#cardTitle").text('Update this data');
            codeid = $(this).attr('rid');
            info_url = url + '/' + codeid + '/edit';
            $.get(info_url, {}, function(d) {
                populateForm(d);
                pagetop();
            });
        });
        //Edit  end

        //Delete
        $("#contentContainer").on('click', '#deleteBtn', function() {
            if (!confirm('Sure?')) return;
            codeid = $(this).attr('rid');
            info_url = url + '/' + codeid;
            $.ajax({
                url: info_url,
                method: "GET",
                type: "DELETE",
                data: {},
                success: function(d) {
                    if (d.success) {
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
                error: function(d) {
                    // console.log(d);
                }
            });
        });
        //Delete  
        function populateForm(data) {
            $("#id_number").val(data.id_number);
            $("#name").val(data.name);
            $("#email").val(data.email);
            $("#phone").val(data.phone);
            $("#vat_reg").val(data.vat_reg);
            $("#address").val(data.address);
            $("#company").val(data.company);
            $("#contract_date").val(data.contract_date);
            $("#codeid").val(data.id);
            $("#addBtn").val('Update');
            $("#addBtn").html('Update');
            $("#addThisFormContainer").show(300);
            $("#newBtn").hide(100);

            var featureImagePreview = document.getElementById('preview-image');
            if (data.image) {
                featureImagePreview.src = '/images/supplier/' + data.image;
            } else {
                featureImagePreview.src = "#";
            }

        }

        function clearform() {
            $('#createThisForm')[0].reset();
            $("#addBtn").val('Create');
            $("#addBtn").html('Create');
            $('#preview-image').attr('src', '#');
            $("#cardTitle").text('Add new data');
        }
    });
</script>

<script>
    $(document).ready(function() {
        $("#contentContainer").on('click', '.pay-btn', function() {
            var id = $(this).data('id');
            var supplierId = $(this).data('supplier-id');
            console.log(supplierId);
            $('#payModal').modal('show');
            $('#payForm').off('submit').on('submit', function(event) {
                event.preventDefault();

                var form_data = new FormData();
                form_data.append("id", id);
                form_data.append("supplierId", supplierId);
                form_data.append("paymentAmount", $("#paymentAmount").val());
                form_data.append("payment_type", $("#payment_type").val());
                form_data.append("paymentNote", $("#paymentNote").val());

                if (!$("#paymentAmount").val()) {
                    alert('Please enter a payment amount.');
                    return;
                }

                var paydoc = document.getElementById('document');
                if (paydoc.files && paydoc.files[0]) {
                    form_data.append("document", paydoc.files[0]);
                }


                $.ajax({
                    url: '{{ URL::to(' / admin / supplier - pay ') }}',
                    method: 'POST',
                    data: form_data,
                    contentType: false,
                    processData: false,
                    // dataType: 'json',
                    success: function(response) {
                        $('#payModal').modal('hide');
                        swal({
                            text: "Payment store successfully",
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

        $('#payModal').on('hidden.bs.modal', function() {
            $('#paymentAmount').val('');
            $('#paymentNote').val('');
        });
    });
</script>

<script>
    $(document).ready(function() {
        $("#image").change(function(e) {
            var reader = new FileReader();
            reader.onload = function(e) {
                $("#preview-image").attr("src", e.target.result);
            };
            reader.readAsDataURL(this.files[0]);
        });
    });
</script>

@endsection