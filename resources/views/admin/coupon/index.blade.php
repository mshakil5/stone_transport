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
                        <h3 class="card-title"  id="cardTitle">Add new data</h3>
                    </div>
                    <div class="card-body">
                        <div class="ermsg"></div>
                        <form id="createThisForm">
                            @csrf
                            <input type="hidden" class="form-control" id="codeid" name="codeid">
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label>Coupon Name <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="coupon_name" name="coupon_name" placeholder="Enter coupon name">
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Coupon Type <span class="text-danger">*</span></label>
                                        <select class="form-control" id="coupon_type">
                                            <option value="">Select Coupon Type</option>
                                            <option value="1">Fixed Amount</option>
                                            <option value="2">Percentage</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Coupon Value <span class="text-danger">*</span></label>
                                        <input type="number" class="form-control" id="coupon_value" placeholder="Enter coupon value">
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Max Use Per User <span class="text-danger">*</span></label>
                                        <input type="number" class="form-control" id="max_use_per_user" placeholder="Enter max use per user" required min="1">
                                    </div>
                                </div>

                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Total Max Use <span class="text-danger">*</span></label>
                                        <input type="number" class="form-control" id="total_max_use" placeholder="Enter total max use" required min="1">
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
                                    <th>Name</th>
                                    <th>Coupon Type</th>
                                    <th>Value</th>
                                    <th>Max Use Per User</th>
                                    <th>Total Max Use</th>
                                    <th>Orders</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($data as $key => $data)
                                <tr>
                                    <td>{{ $key + 1 }}</td>
                                    <td>{{ $data->coupon_name }}</td>
                                    <td>
                                        @if($data->coupon_type == 1)
                                            Fixed Amount
                                        @else
                                            Percentage
                                        @endif
                                    </td>
                                    <td>{{ $data->coupon_value }}</td>
                                    <td>{{ $data->max_use_per_user }}</td>
                                    <td>{{ $data->total_max_use }}</td>
                                    <td>
                                        <a href="{{ route('getallorderbycoupon', $data->id) }}" title="View Orders">
                                            <i class="fa fa-shopping-cart" style="color: #2196f3; font-size:16px;"></i>
                                        </a>
                                    </td>

                                    <td>
                                        <div class="custom-control custom-switch">
                                            <input type="checkbox" class="custom-control-input toggle-status" id="customSwitchStatus{{ $data->id }}" data-id="{{ $data->id }}" {{ $data->status == 1 ? 'checked' : '' }}>
                                            <label class="custom-control-label" for="customSwitchStatus{{ $data->id }}"></label>
                                        </div>
                                    </td>
                                    <td>
                                        <a id="EditBtn" rid="{{ $data->id }}">
                                            <i class="fa fa-edit" style="color: #2196f3; font-size:16px;"></i>
                                        </a>
                                        <a id="deleteBtn" rid="{{ $data->id }}">
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
      $('#example2').DataTable({
        "paging": true,
        "lengthChange": false,
        "searching": false,
        "ordering": true,
        "info": true,
        "autoWidth": false,
        "responsive": true,
      });
    });
</script>

<script>
    $(document).ready(function() {
        $('.toggle-status').change(function() {
            var brand_id = $(this).data('id');
            var status = $(this).prop('checked') ? 1 : 0;

            $.ajax({
                url: '/admin/coupon-status',
                method: "POST",
                data: {
                    brand_id: brand_id,
                    status: status,
                    _token: "{{ csrf_token() }}"
                },
                success: function(d) {
                    swal({
                        text: "Status Chnaged successfully",
                        icon: "success",
                        button: {
                            text: "OK",
                            className: "swal-button--confirm"
                        }
                    });
                },
                error: function(xhr, status, error) {
                    console.error(xhr.responseText);
                }
            });
        });
    });
</script>

<script>
  $(document).ready(function () {
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

      $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });
      //
      var url = "{{URL::to('/admin/coupon')}}";
      var upurl = "{{URL::to('/admin/coupon-update')}}";

      $("#addBtn").click(function(){

          //create
          if($(this).val() == 'Create') {
              var form_data = new FormData();
              form_data.append("coupon_name", $("#coupon_name").val());
              form_data.append("coupon_type", $("#coupon_type").val());
              form_data.append("coupon_value", $("#coupon_value").val());
              form_data.append("max_use_per_user", $("#max_use_per_user").val());
              form_data.append("total_max_use", $("#total_max_use").val());

              $.ajax({
                url: url,
                method: "POST",
                contentType: false,
                processData: false,
                data:form_data,
                success: function (d) {
                    if (d.status == 303) {
                        $(".ermsg").html(d.message);
                    }else if(d.status == 300){
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
          if($(this).val() == 'Update'){
              var form_data = new FormData();
              form_data.append("coupon_name", $("#coupon_name").val());
              form_data.append("coupon_type", $("#coupon_type").val());
              form_data.append("coupon_value", $("#coupon_value").val());
              form_data.append("max_use_per_user", $("#max_use_per_user").val());
              form_data.append("total_max_use", $("#total_max_use").val());
              form_data.append("codeid", $("#codeid").val());
              
              $.ajax({
                  url:upurl,
                  type: "POST",
                  dataType: 'json',
                  contentType: false,
                  processData: false,
                  data:form_data,
                  success: function(d){
                    //   console.log(d);
                      if (d.status == 303) {
                          $(".ermsg").html(d.message);
                          pagetop();
                      }else if(d.status == 300){
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
      $("#contentContainer").on('click','#EditBtn', function(){
          $("#cardTitle").text('Update this data');
          codeid = $(this).attr('rid');
          info_url = url + '/'+codeid+'/edit';
          $.get(info_url,{},function(d){
              populateForm(d);
              pagetop();
          });
      });
      //Edit  end

      //Delete
      $("#contentContainer").on('click','#deleteBtn', function(){
            if(!confirm('Sure?')) return;
            codeid = $(this).attr('rid');
            info_url = url + '/'+codeid;
            $.ajax({
                url:info_url,
                method: "GET",
                type: "DELETE",
                data:{
                },
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
                error:function(d){
                    // console.log(d);
                }
            });
        });
      //Delete  
      function populateForm(data){
          $("#coupon_name").val(data.coupon_name);
          $("#coupon_type").val(data.coupon_type);
          $("#coupon_value").val(data.coupon_value);
          $("#max_use_per_user").val(data.max_use_per_user);
          $("#total_max_use").val(data.total_max_use);
          $("#codeid").val(data.id);
          $("#addBtn").val('Update');
          $("#addBtn").html('Update');
          $("#addThisFormContainer").show(300);
          $("#newBtn").hide(100);
      }
      function clearform(){
          $('#createThisForm')[0].reset();
          $("#addBtn").val('Create');
          $("#addBtn").html('Create');
          $("#cardTitle").text('Add new data');
      }
  });
</script>

@endsection