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
                <div class="col-md-8 offset-md-2">
                    <div class="card">
                        <div class="card-header bg-secondary text-white">
                            <h3 class="card-title">Update Role</h3>
                        </div>
                        <div class="card-body">
                            <div class="ermsg"></div>
                            <form action="" method="post" id="permissionForm" enctype="multipart/form-data">
                                {{ csrf_field() }}
                                <div class="form-group">
                                    <label for="name" class="control-label">Role Name</label>
                                    <input name="name" id="name" type="text" class="form-control" maxlength="50" value="{{ $data->name }}" required />
                                    <input name="id" id="id" type="hidden" class="form-control" value="{{ $data->id }}" required />
                                </div>
                                
                                <div class="row">

                                    <div class="col-md-6">
                                        <div class="form-group">

                                            <fieldset class="border p-2">
                                                <legend class="w-auto px-2">Dashboard</legend>
                                                <div class="form-check">
                                                  <input class="form-check-input" type="checkbox" id="p1" name="permission[]" value="1"
                                                      @foreach (json_decode($data->permission) as $permission) 
                                                          @if ($permission == 1) checked @endif 
                                                      @endforeach>
                                                  <label class="form-check-label" for="p1">Dashboard Content</label>
                                                </div>
                                            </fieldset>

                                            <fieldset class="border p-2">
                                                <legend class="w-auto px-2">Product</legend>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="p2" name="permission[]" value="2"
                                                        @foreach (json_decode($data->permission) as $permission) @if ($permission == 2) checked @endif @endforeach>
                                                    <label class="form-check-label" for="p2">Create Product</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="p3" name="permission[]" value="3"
                                                        @foreach (json_decode($data->permission) as $permission) @if ($permission == 3) checked @endif @endforeach>
                                                    <label class="form-check-label" for="p3">Manage Product</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="p4" name="permission[]" value="4"
                                                        @foreach (json_decode($data->permission) as $permission) @if ($permission == 4) checked @endif @endforeach>
                                                    <label class="form-check-label" for="p4">Edit Product</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="p5" name="permission[]" value="5"
                                                        @foreach (json_decode($data->permission) as $permission) @if ($permission == 5) checked @endif @endforeach>
                                                    <label class="form-check-label" for="p5">Category</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="p30" name="permission[]" value="30"
                                                        @foreach (json_decode($data->permission) as $permission) @if ($permission == 30) checked @endif @endforeach>
                                                    <label class="form-check-label" for="p30">Unit</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="p6" name="permission[]" value="6"
                                                        @foreach (json_decode($data->permission) as $permission) @if ($permission == 6) checked @endif @endforeach>
                                                    <label class="form-check-label" for="p6">Group</label>
                                                </div>
                                            </fieldset>

                                            <fieldset class="border p-2">
                                                <legend class="w-auto px-2">Purchase</legend>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="p7" name="permission[]" value="7"
                                                        @foreach (json_decode($data->permission) as $permission) @if ($permission == 7) checked @endif @endforeach>
                                                    <label class="form-check-label" for="p7">Create Order</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="p8" name="permission[]" value="8"
                                                        @foreach (json_decode($data->permission) as $permission) @if ($permission == 8) checked @endif @endforeach>
                                                    <label class="form-check-label" for="p8">Order List</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="p9" name="permission[]" value="9"
                                                        @foreach (json_decode($data->permission) as $permission) @if ($permission == 9) checked @endif @endforeach>
                                                    <label class="form-check-label" for="p9">Add To Stock From Order List</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="p10" name="permission[]" value="10"
                                                        @foreach (json_decode($data->permission) as $permission) @if ($permission == 10) checked @endif @endforeach>
                                                    <label class="form-check-label" for="p10">Purchase History</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="p11" name="permission[]" value="11"
                                                        @foreach (json_decode($data->permission) as $permission) @if ($permission == 11) checked @endif @endforeach>
                                                    <label class="form-check-label" for="p11">Return History</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="p31" name="permission[]" value="31"
                                                        @foreach (json_decode($data->permission) as $permission) @if ($permission == 31) checked @endif @endforeach>
                                                    <label class="form-check-label" for="p31">Supplier</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="p12" name="permission[]" value="12"
                                                        @foreach (json_decode($data->permission) as $permission) @if ($permission == 12) checked @endif @endforeach>
                                                    <label class="form-check-label" for="p12">Supplier Pay</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="p13" name="permission[]" value="13"
                                                        @foreach (json_decode($data->permission) as $permission) @if ($permission == 13) checked @endif @endforeach>
                                                    <label class="form-check-label" for="p13">Mother Vessel</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="p14" name="permission[]" value="14"
                                                        @foreach (json_decode($data->permission) as $permission) @if ($permission == 14) checked @endif @endforeach>
                                                    <label class="form-check-label" for="p14">Lighter Vessel</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="p15" name="permission[]" value="15"
                                                        @foreach (json_decode($data->permission) as $permission) @if ($permission == 15) checked @endif @endforeach>
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
                                                <input class="form-check-input" type="checkbox" id="p16" name="permission[]" value="16"
                                                    @foreach (json_decode($data->permission) as $permission) @if ($permission == 16) checked @endif @endforeach>
                                                <label class="form-check-label" for="p16">Stock List</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="p17" name="permission[]" value="17"
                                                    @foreach (json_decode($data->permission) as $permission) @if ($permission == 17) checked @endif @endforeach>
                                                <label class="form-check-label" for="p17">Stock Ledger</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="p18" name="permission[]" value="18"
                                                    @foreach (json_decode($data->permission) as $permission) @if ($permission == 18) checked @endif @endforeach>
                                                <label class="form-check-label" for="p18">Stocking History</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="p19" name="permission[]" value="19"
                                                    @foreach (json_decode($data->permission) as $permission) @if ($permission == 19) checked @endif @endforeach>
                                                <label class="form-check-label" for="p19">System Loss</label>
                                            </div>    
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="p32" name="permission[]" value="32"
                                                    @foreach (json_decode($data->permission) as $permission) @if ($permission == 32) checked @endif @endforeach>
                                                <label class="form-check-label" for="p32">Warehouse</label>
                                            </div>                                   
                                        </fieldset>


                                        <fieldset class="border p-2">
                                            <legend class="w-auto px-2">Sales</legend>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="p20" name="permission[]" value="20"
                                                    @foreach (json_decode($data->permission) as $permission) @if ($permission == 20) checked @endif @endforeach>
                                                <label class="form-check-label" for="p20">All Orders</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="p21" name="permission[]" value="21"
                                                    @foreach (json_decode($data->permission) as $permission) @if ($permission == 21) checked @endif @endforeach>
                                                <label class="form-check-label" for="p21">Processing Orders</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="p22" name="permission[]" value="22"
                                                    @foreach (json_decode($data->permission) as $permission) @if ($permission == 22) checked @endif @endforeach>
                                                <label class="form-check-label" for="p22">Delivered Orders</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="p23" name="permission[]" value="23"
                                                    @foreach (json_decode($data->permission) as $permission) @if ($permission == 23) checked @endif @endforeach>
                                                <label class="form-check-label" for="p23">Cancelled Orders</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="p24" name="permission[]" value="24"
                                                    @foreach (json_decode($data->permission) as $permission) @if ($permission == 24) checked @endif @endforeach>
                                                <label class="form-check-label" for="p24">In House Sale</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="p25" name="permission[]" value="25"
                                                    @foreach (json_decode($data->permission) as $permission) @if ($permission == 25) checked @endif @endforeach>
                                                <label class="form-check-label" for="p25">In House Sales List</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="p26" name="permission[]" value="26"
                                                    @foreach (json_decode($data->permission) as $permission) @if ($permission == 26) checked @endif @endforeach>
                                                <label class="form-check-label" for="p26">Quotation List</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="p27" name="permission[]" value="27"
                                                    @foreach (json_decode($data->permission) as $permission) @if ($permission == 27) checked @endif @endforeach>
                                                <label class="form-check-label" for="p27">Whole Saler</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="p28" name="permission[]" value="28"
                                                    @foreach (json_decode($data->permission) as $permission) @if ($permission == 28) checked @endif @endforeach>
                                                <label class="form-check-label" for="p28">Whole Saler Due Receive</label>
                                            </div>
                                        </fieldset>

                                        <fieldset class="border p-2">
                                            <legend class="w-auto px-2">Additional Permissions</legend>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="p29" name="permission[]" value="29"
                                                    @foreach (json_decode($data->permission) as $permission) @if ($permission == 29) checked @endif @endforeach>
                                                <label class="form-check-label" for="p29">Reports</label>
                                            </div>
                                        </fieldset>

                                      </div>
                                  </div>
                                </div>
                                 <div class="row">
                                    <div class="col-md-12 text-center">
                                        <a href="{{ route('admin.role') }}" class="btn btn-secondary">Back</a>
                                        <button type="submit" class="btn btn-primary" id="updateBtn">Update</button>
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

        var url = "{{URL::to('/admin/role-update')}}";
        $("body").delegate("#updateBtn","click",function(event){
                event.preventDefault();

                var name = $("#name").val();
                var id = $("#id").val();
                var permission = $("input:checkbox:checked[name='permission[]']")
                    .map(function(){return $(this).val();}).get();

                $.ajax({
                    url: url,
                    method: "POST",
                    data: {id,name,permission},

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
    