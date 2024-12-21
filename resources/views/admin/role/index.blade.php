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
                            <h3 class="card-title">Create Role</h3>
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
                                                    <label class="form-check-label" for="p1">Dashboard</label>
                                                </div>
                                            </fieldset>

                                            <fieldset class="border p-2">
                                                <legend class="w-auto px-2">Order</legend>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="p2" name="permission[]" value="2">
                                                    <label class="form-check-label" for="p2">Order</label>
                                                </div>
                                            </fieldset>

                                            <fieldset class="border p-2">
                                                <legend class="w-auto px-2">Admin</legend>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="p3" name="permission[]" value="3">
                                                    <label class="form-check-label" for="p3">Create Admin</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="p4" name="permission[]" value="4">
                                                    <label class="form-check-label" for="p4">Edit Admin</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="p5" name="permission[]" value="5">
                                                    <label class="form-check-label" for="p5">Delete Admin</label>
                                                </div>
                                            </fieldset>

                                            <fieldset class="border p-2">
                                                <legend class="w-auto px-2">Customer</legend>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="p6" name="permission[]" value="6">
                                                    <label class="form-check-label" for="p6">Create Customer</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="p7" name="permission[]" value="7">
                                                    <label class="form-check-label" for="p7">Edit Customer</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="p8" name="permission[]" value="8">
                                                    <label class="form-check-label" for="p8">Delete Customer</label>
                                                </div>
                                            </fieldset>

                                            <fieldset class="border p-2">
                                                <legend class="w-auto px-2">Product</legend>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="p9" name="permission[]" value="9">
                                                    <label class="form-check-label" for="p9">Add Product</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="p10" name="permission[]" value="10">
                                                    <label class="form-check-label" for="p10">Edit Product</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="p11" name="permission[]" value="11">
                                                    <label class="form-check-label" for="p11">Delete Product</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="p12" name="permission[]" value="12">
                                                    <label class="form-check-label" for="p12">Category</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="p13" name="permission[]" value="13">
                                                    <label class="form-check-label" for="p13">Brand</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="p14" name="permission[]" value="14">
                                                    <label class="form-check-label" for="p14">Model</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="p15" name="permission[]" value="15">
                                                    <label class="form-check-label" for="p15">Unit</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="p16" name="permission[]" value="16">
                                                    <label class="form-check-label" for="p16">Group</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="p17" name="permission[]" value="17">
                                                    <label class="form-check-label" for="p17">Bundle Product</label>
                                                </div>
                                            </fieldset>

                                            <fieldset class="border p-2">
                                                <legend class="w-auto px-2">Slider</legend>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="p18" name="permission[]" value="18">
                                                    <label class="form-check-label" for="p18">Slider</label>
                                                </div>
                                            </fieldset>

                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            
                                            <fieldset class="border p-2">
                                                <legend class="w-auto px-2">Stock</legend>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="p19" name="permission[]" value="19">
                                                    <label class="form-check-label" for="p19">Supplier</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="p20" name="permission[]" value="20">
                                                    <label class="form-check-label" for="p20">Supplier Pay</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="p21" name="permission[]" value="21">
                                                    <label class="form-check-label" for="p21">Supplier Transaction</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="p22" name="permission[]" value="22">
                                                    <label class="form-check-label" for="p22">Purchase</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="p23" name="permission[]" value="23">
                                                    <label class="form-check-label" for="p23">Stock List</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="p24" name="permission[]" value="24">
                                                    <label class="form-check-label" for="p24">System Loss</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="p25" name="permission[]" value="25">
                                                    <label class="form-check-label" for="p25">Purchase History</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="p26" name="permission[]" value="26">
                                                    <label class="form-check-label" for="p26">Purchase Return</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="p27" name="permission[]" value="27">
                                                    <label class="form-check-label" for="p27">Return History</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="p28" name="permission[]" value="28">
                                                    <label class="form-check-label" for="p28">System Loses</label>
                                                </div>
                                            </fieldset>

                                            <fieldset class="border p-2">
                                                <legend class="w-auto px-2">Company</legend>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="p29" name="permission[]" value="29">
                                                    <label class="form-check-label" for="p29">Company Details</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="p30" name="permission[]" value="30">
                                                    <label class="form-check-label" for="p30">Contact Email</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="p31" name="permission[]" value="31">
                                                    <label class="form-check-label" for="p31">Contact Message</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="p32" name="permission[]" value="32">
                                                    <label class="form-check-label" for="p32">Section Status</label>
                                                </div>
                                            </fieldset>

                                            <fieldset class="border p-2">
                                                <legend class="w-auto px-2">Additional Permissions</legend>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="p33" name="permission[]" value="33">
                                                    <label class="form-check-label" for="p33">Ad</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="p34" name="permission[]" value="34">
                                                    <label class="form-check-label" for="p34">Coupon</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="p35" name="permission[]" value="35">
                                                    <label class="form-check-label" for="p35">Create Special Offer</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="p36" name="permission[]" value="36">
                                                    <label class="form-check-label" for="p36">Edit Special Offer</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="p37" name="permission[]" value="37">
                                                    <label class="form-check-label" for="p37">Create Flash Sale</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="p38" name="permission[]" value="38">
                                                    <label class="form-check-label" for="p38">Edit Flash Sale</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="p39" name="permission[]" value="39">
                                                    <label class="form-check-label" for="p39">In House Sale</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="p40" name="permission[]" value="40">
                                                    <label class="form-check-label" for="p40">In House Orders</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="p41" name="permission[]" value="41">
                                                    <label class="form-check-label" for="p41">Delivery Man</label>
                                                </div>
                                                    <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="p42" name="permission[]" value="42">
                                                    <label class="form-check-label" for="p42">Reports</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="p43" name="permission[]" value="43">
                                                    <label class="form-check-label" for="p43">Role & Permission</label>
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
    