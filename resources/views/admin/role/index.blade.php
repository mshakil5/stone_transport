@extends('admin.layouts.admin')

@section('content')
    <meta name="csrf-token" content="{{ csrf_token() }}"/>

    @if ($errors->any())
        <div class="alert alert-danger">
            <a href="#" class="close" data-dismiss="alert" aria-label="close">×</a>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if (Session::has('success'))
        <div class="alert alert-success">
            <a href="#" class="close" data-dismiss="alert" aria-label="close">×</a>
            <p>{{ Session::get('success') }}</p>
        </div>
        {{ Session::forget('success') }}
    @endif

    <style>
        .form-check-input {
            position: absolute;
            opacity: 0;
        }

        .form-check-input + .form-check-label {
            position: relative;
            padding-left: 50px;
            cursor: pointer;
            font-size: 1rem;
            user-select: none;
        }

        .form-check-input + .form-check-label::before {
            content: '';
            position: absolute;
            left: 0;
            top: 50%;
            transform: translateY(-50%);
            width: 40px;
            height: 20px;
            background-color: #ccc;
            border-radius: 20px;
            transition: background-color 0.3s;
        }

        .form-check-input + .form-check-label::after {
            content: '';
            position: absolute;
            left: 2px;
            top: 50%;
            transform: translateY(-50%);
            width: 16px;
            height: 16px;
            background-color: white;
            border-radius: 50%;
            transition: transform 0.3s;
        }

        .form-check-input:checked + .form-check-label::before {
            background-color: #007bff;
        }

        .form-check-input:checked + .form-check-label::after {
            transform: translate(20px, -50%);
        }
    </style>
    

    <section class="content pt-3" id="contentContainer">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header bg-secondary text-white">
                            <h3 class="card-title">Roles and Permissions</h3>
                        </div>
                        <div class="card-body">
                            <table class="table table-hover table-responsive">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach (\App\Models\Role::all() as $data)
                                        <tr>
                                            <td>{{ $data->name }}</td>
                                            <td>
                                                <a href="{{ route('admin.roleedit', $data->id) }}" class="btn btn-success btn-sm">
                                                    <i class='fa fa-pencil'></i> Edit
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header bg-secondary text-white">
                            <h3 class="card-title">Create New Role</h3>
                        </div>
                        <div class="card-body">
                            <div class="ermsg"></div>
                            <form action="" method="post" id="permissionForm" enctype="multipart/form-data">
                                {{ csrf_field() }}
                                <div class="form-group">
                                    <label for="name" class="control-label">Role Name</label>
                                    <input name="name" id="name" type="text" class="form-control" maxlength="50" required  placeholder="Enter Role Name"/>
                                </div>
                                <div class="row">

                                    <div class="col-md-6">
                                        <div class="form-group">

                                            <fieldset class="border p-2">
                                                <legend class="w-auto px-2">Dashboard</legend>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="p1" name="permission[]" value="1">
                                                    <label class="form-check-label" for="p1">Dashboard Content</label>
                                                </div>
                                            </fieldset>

                                            <fieldset class="border p-2">
                                                <legend class="w-auto px-2">Product</legend>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="p2" name="permission[]" value="2">
                                                    <label class="form-check-label" for="p2">Add Product</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="p3" name="permission[]" value="3">
                                                    <label class="form-check-label" for="p3">Edit Product</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="p4" name="permission[]" value="4">
                                                    <label class="form-check-label" for="p4">Delete Product</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="p5" name="permission[]" value="5">
                                                    <label class="form-check-label" for="p5">Category</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="p6" name="permission[]" value="6">
                                                    <label class="form-check-label" for="p6">Group</label>
                                                </div>
                                            </fieldset>

                                            <fieldset class="border p-2">
                                                <legend class="w-auto px-2">Purchase</legend>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="p7" name="permission[]" value="7">
                                                    <label class="form-check-label" for="p7">Create Order</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="p8" name="permission[]" value="8">
                                                    <label class="form-check-label" for="p8">Order List</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="p9" name="permission[]" value="9">
                                                    <label class="form-check-label" for="p9">Add To Stock From Order List</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="p10" name="permission[]" value="10">
                                                    <label class="form-check-label" for="p10">Purchase History</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="p11" name="permission[]" value="11">
                                                    <label class="form-check-label" for="p11">Return History</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="p11" name="permission[]" value="11">
                                                    <label class="form-check-label" for="p11">Supplier</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="p12" name="permission[]" value="12">
                                                    <label class="form-check-label" for="p12">Supplier Pay</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="p13" name="permission[]" value="13">
                                                    <label class="form-check-label" for="p13">Mother Vessel</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="p14" name="permission[]" value="14">
                                                    <label class="form-check-label" for="p14">Lighter Vessel</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="p15" name="permission[]" value="15">
                                                    <label class="form-check-label" for="p15">Ghat</label>
                                                </div>
                                            </fieldset>

                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">

                                          <fieldset class="border p-2">
                                              <legend class="w-auto px-2">Stock</legend>
                                              <div class="form-check">
                                                  <input class="form-check-input" type="checkbox" id="p16" name="permission[]" value="16">
                                                  <label class="form-check-label" for="p16">Stock List</label>
                                              </div>
                                              <div class="form-check">
                                                  <input class="form-check-input" type="checkbox" id="p17" name="permission[]" value="17">
                                                  <label class="form-check-label" for="p17">Stock Ledger</label>
                                              </div>
                                              <div class="form-check">
                                                  <input class="form-check-input" type="checkbox" id="p18" name="permission[]" value="18">
                                                  <label class="form-check-label" for="p18">Stocking History</label>
                                              </div>
                                              <div class="form-check">
                                                  <input class="form-check-input" type="checkbox" id="p19" name="permission[]" value="19">
                                                  <label class="form-check-label" for="p19">System Loss</label>
                                              </div>                                     
                                          </fieldset>

                                          <fieldset class="border p-2">
                                              <legend class="w-auto px-2">Sales</legend>
                                              <div class="form-check">
                                                  <input class="form-check-input" type="checkbox" id="p20" name="permission[]" value="20">
                                                  <label class="form-check-label" for="p20">All Orders</label>
                                              </div>
                                              <div class="form-check">
                                                  <input class="form-check-input" type="checkbox" id="p21" name="permission[]" value="21">
                                                  <label class="form-check-label" for="p21">Processing Orders</label>
                                              </div>
                                              <div class="form-check">
                                                  <input class="form-check-input" type="checkbox" id="p22" name="permission[]" value="22">
                                                  <label class="form-check-label" for="p22">Delivered Orders</label>
                                              </div>
                                              <div class="form-check">
                                                  <input class="form-check-input" type="checkbox" id="p23" name="permission[]" value="23">
                                                  <label class="form-check-label" for="p23">Cancelled Orders</label>
                                              </div>
                                              <div class="form-check">
                                                  <input class="form-check-input" type="checkbox" id="p24" name="permission[]" value="24">
                                                  <label class="form-check-label" for="p24">In House Sale</label>
                                              </div>
                                              <div class="form-check">
                                                  <input class="form-check-input" type="checkbox" id="p25" name="permission[]" value="25">
                                                  <label class="form-check-label" for="p25">In House Sales List</label>
                                              </div>
                                              <div class="form-check">
                                                  <input class="form-check-input" type="checkbox" id="p26" name="permission[]" value="26">
                                                  <label class="form-check-label" for="p26">Quotation List</label>
                                              </div>
                                              <div class="form-check">
                                                  <input class="form-check-input" type="checkbox" id="p27" name="permission[]" value="27">
                                                  <label class="form-check-label" for="p27">Whole Saler</label>
                                              </div>
                                              <div class="form-check">
                                                  <input class="form-check-input" type="checkbox" id="p28" name="permission[]" value="28">
                                                  <label class="form-check-label" for="p28">Whole Saler Due Receive</label>
                                              </div>
                                          </fieldset>

                                          <fieldset class="border p-2">
                                              <legend class="w-auto px-2">Additional Permissions</legend>
                                              <div class="form-check">
                                                  <input class="form-check-input" type="checkbox" id="p29" name="permission[]" value="29">
                                                  <label class="form-check-label" for="p29">Reports</label>
                                              </div>
                                          </fieldset>

                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-12 text-center">
                                        <button class="btn btn-success btn-md" id="submitBtn" type="submit">
                                            <i class="fa fa-plus-circle"></i> Submit
                                        </button>
                                    </div>
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
    $(document).ready(function () {

        $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });

        var url = "{{ route('admin.rolestore') }}";

        $("body").delegate("#submitBtn","click",function(event){
                event.preventDefault();

                var name = $("#name").val();
                var permission = $("input:checkbox:checked[name='permission[]']")
                    .map(function(){return $(this).val();}).get();

                console.log(permission, name);

                $.ajax({
                    url: url,
                    method: "POST",
                    data: {name,permission},

                    success: function (d) {
                        if (d.status == 303) {
                            $(".ermsg").html(d.message);
                            pagetop();
                        }else if(d.status == 300){
                            $(".ermsg").html(d.message);
                            pagetop();
                            window.setTimeout(function(){location.reload()},2000)
                            
                        }
                    },
                    error: function (d) {
                        console.log(d);
                    }
                });
        });

    });  
</script>

@endsection
    