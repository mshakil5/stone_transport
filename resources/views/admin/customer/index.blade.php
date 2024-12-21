@extends('admin.layouts.admin')

@section('content')

<!-- Main content -->
<section class="content" id="newBtnSection">
    <div class="container-fluid">
      <div class="row">
        <div class="col-2">
            <button type="button" class="btn btn-secondary my-3" id="newBtn">Add new</button>
            <a href="{{ route('admin.all.due.list') }}" class="btn btn-secondary">Due List</a>
        </div>
      </div>
    </div>
</section>
  <!-- /.content -->



    <!-- Main content -->
    <section class="content mt-3" id="addThisFormContainer">
      <div class="container-fluid">
        <div class="row justify-content-md-center">
          <!-- right column -->
          <div class="col-md-8">
            <!-- general form elements disabled -->
            <div class="card card-secondary">
              <div class="card-header">
                <h3 class="card-title" id="cardTitle">Add new data</h3>
              </div>
              <!-- /.card-header -->
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
                        <label>Surname <span style="color: red;">*</span></label>
                        <input type="text" id="surname" name="surname" class="form-control" placeholder="Enter surname">
                      </div>
                    </div>
                  </div>

                  <div class="row">
                    <div class="col-sm-6">
                      <div class="form-group">
                        <label>Email <span style="color: red;">*</span></label>
                        <input type="email" class="form-control" id="email" name="email" placeholder="Enter email">
                      </div>
                    </div>
                    <div class="col-sm-6">
                      <div class="form-group">
                        <label>Phone <span style="color: red;">*</span></label>
                        <input type="number" id="phone" name="phone" class="form-control" placeholder="Enter phone">
                      </div>
                    </div>
                  </div>

                  <div class="row d-none">
                    <div class="col-sm-6">
                      <div class="form-group">
                        <label>Password</label>
                        <input type="password" class="form-control" id="password" name="password" placeholder="Enter password" value="123456">
                      </div>
                    </div>
                    <div class="col-sm-6">
                      <div class="form-group">
                        <label>Confirm Password</label>
                        <input type="password" id="confirm_password" name="confirm_password" class="form-control" placeholder="Enter confirm password" value="123456">
                      </div>
                    </div>
                  </div>
                  
                </form>
              </div>

              
              <!-- /.card-body -->
              <div class="card-footer">
                <button type="submit" id="addBtn" class="btn btn-secondary" value="Create">Create</button>
                <button type="submit" id="FormCloseBtn" class="btn btn-default">Cancel</button>
              </div>
              <!-- /.card-footer -->
              <!-- /.card-body -->
            </div>
          </div>
          <!--/.col (right) -->
        </div>
        <!-- /.row -->
      </div><!-- /.container-fluid -->
    </section>
    <!-- /.content -->


<!-- Main content -->
<section class="content" id="contentContainer">
    <div class="container-fluid">
      <div class="row">
        <div class="col-12">
          <!-- /.card -->

          <div class="card card-secondary">
            <div class="card-header">
              <h3 class="card-title">All Data</h3>
            </div>
            <!-- /.card-header -->
            <div class="card-body">
              <table id="example1" class="table table-bordered table-striped">
                <thead>
                <tr>
                  <th>Sl</th>
                  <th>Name/Email/Number</th>
                  <!-- <th>Balance</th> -->
                  <th>Transactions</th>
                  <th>Sales</th>
                  <th>Due Amount</th>
                  <th>Active</th>
                  <th>Action</th>
                </tr>
                </thead>
                <tbody>
                  @foreach ($data as $key => $data)
                  <tr>
                    <td>{{ $key + 1 }}</td>
                    <td>{{$data->name}} {{$data->surname}} <br> {{$data->email}} <br> {{$data->phone}}</td>
                    {{-- 
                    <td>
                        <div class="align-items-center">

                          @php
                              $netAmount = $data->total_increament - $data->total_decrement;
                          @endphp

                          @if ($netAmount > 0)
                              <span class="btn btn-sm btn-danger">£ {{ number_format($netAmount, 2) }}</span>
                              <button class="btn btn-sm btn-warning pay-btn" data-id="{{ $data->id }}" data-customer-id="{{ $data->id }}">Receive</button>
                          @endif
                        </div>
                      <input type="hidden" id="customerId" name="customerId">  
                    </td>
                    --}}
                    <td>
                      <a href="{{ route('customer.transactions', ['wholeSalerId' => $data->id]) }}" class="btn btn-info">
                          Transactions
                      </a>
                    </td>
                    <td>
                        @if ($data->sales_count > 0)
                            <a href="{{ route('getallorder', $data->id) }}" class="btn btn-info">
                                Sales ({{ $data->sales_count }})
                            </a>
                        @else
                            0
                        @endif
                    </td>
                    <td>
                    @php
                      $dueAmount = $data->total_increament - $data->total_decrement;
                    @endphp

                    @if ($dueAmount > 0)
                      <a href="{{ route('admin.due.list', $data->id) }}" class="btn btn-danger">
                            Due Amount({{ number_format($dueAmount, 2) }})
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
                      <a class="btn btn-app" href="{{route('customer.email', $data->id)}}">
                          <i class="fas fa-envelope"></i> Email
                      </a>
                      <a class="btn btn-app" id="EditBtn" rid="{{ $data->id }}">
                          <i class="fas fa-edit"></i> Edit
                      </a>
                      
                      <a class="btn btn-app" id="deleteBtn" rid="{{ $data->id }}">
                          <i class="fa fa-trash-o" style="color: red; font-size:16px;"></i>Delete
                      </a>
                    </td>
                  </tr>
                  @endforeach
                
                </tbody>
              </table>
            </div>
            <!-- /.card-body -->
          </div>
          <!-- /.card -->
        </div>
        <!-- /.col -->
      </div>
      <!-- /.row -->
    </div>
    <!-- /.container-fluid -->
</section>
<!-- /.content -->

<div class="modal fade" id="payModal" tabindex="-1" role="dialog" aria-labelledby="payModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="payModalLabel">WholeSaler Payment Form</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="payForm">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="paymentAmount">Payment Amount</label>
                        <input type="number" class="form-control" id="paymentAmount" name="paymentAmount" placeholder="Enter payment amount">
                    </div>
                    
                    <div class="form-group">
                        <label for="document">Document</label>
                        <input type="file" class="form-control-file" id="document" name="document">
                    </div>

                    <div class="form-group">
                        <label for="payment_type">Payment Type</label>
                        <select name="payment_type" id="payment_type" class="form-control" >
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


@endsection
@section('script')

<script>
    $(document).ready(function () {
        $("#contentContainer").on('click', '.pay-btn', function () {
            var id = $(this).data('id');
            var customerId = $(this).data('customer-id');
            $('#payModal').modal('show');
            $('#payForm').off('submit').on('submit', function (event) {
                event.preventDefault();

                var form_data = new FormData();
                form_data.append("id", id);
                form_data.append("customerId", customerId);
                form_data.append("paymentAmount", $("#paymentAmount").val());
                form_data.append("payment_type", $("#payment_type").val());
                form_data.append("paymentNote", $("#paymentNote").val());

                var paydoc = document.getElementById('document');
                    if(paydoc.files && paydoc.files[0]) {
                        form_data.append("document", paydoc.files[0]);
                    }


                $.ajax({
                    url: '{{ URL::to('/admin/customer-pay') }}',
                    method: 'POST',
                    data:form_data,
                    contentType: false,
                    processData: false,
                    // dataType: 'json',
                    success: function (response) {
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
                    error: function (xhr) {
                        console.log(xhr.responseText);
                    }
                });
            });
        });

        $('#payModal').on('hidden.bs.modal', function () {
            $('#paymentAmount').val('');
            $('#paymentNote').val('');
        });
    });
</script>

<script>
    $(function () {
      $("#example1").DataTable({
        "responsive": true, "lengthChange": false, "autoWidth": false,
        "buttons": ["copy", "csv", "excel", "pdf", "print"],
        "lengthMenu": [[100, "All", 50, 25], [100, "All", 50, 25]]
      }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
    });
</script>

<script>
  // customer status change 
  $(document).ready(function() {
    $('.toggle-status').change(function() {
        var isChecked = $(this).is(':checked');
        var customerId = $(this).data('id');

        $.ajax({
            url: '/admin/toggle-customer-status',
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                id: customerId,
                status: isChecked ? 1 : 0
            },
            success: function(response) {
                swal({
                    text: "Status updated successfully",
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
      //header for csrf-token is must in laravel
      $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });
      //
      var url = "{{URL::to('/admin/whole-saler')}}";
      var upurl = "{{URL::to('/admin/whole-saler-update')}}";
      // console.log(url);
      $("#addBtn").click(function(){
      //   alert("#addBtn");
          if($(this).val() == 'Create') {
              var form_data = new FormData();
              form_data.append("name", $("#name").val());
              form_data.append("email", $("#email").val());
              form_data.append("phone", $("#phone").val());
              form_data.append("surname", $("#surname").val());
              form_data.append("password", $("#password").val());
              form_data.append("confirm_password", $("#confirm_password").val());
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
                error: function (d) {
                    console.log(d);
                }
            });
          }
          //create  end
          //Update
          if($(this).val() == 'Update'){
              var form_data = new FormData();
              form_data.append("name", $("#name").val());
              form_data.append("email", $("#email").val());
              form_data.append("phone", $("#phone").val());
              form_data.append("surname", $("#surname").val());
              form_data.append("password", $("#password").val());
              form_data.append("confirm_password", $("#confirm_password").val());
              form_data.append("codeid", $("#codeid").val());
              
              $.ajax({
                  url:upurl,
                  type: "POST",
                  dataType: 'json',
                  contentType: false,
                  processData: false,
                  data:form_data,
                  success: function(d){
                      console.log(d);
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
                  error:function(d){
                      console.log(d);
                  }
              });
          }
          //Update
      });
      //Edit
      $("#contentContainer").on('click','#EditBtn', function(){
          $("#cardTitle").text('Update this data');
          //alert("btn work");
          codeid = $(this).attr('rid');
          //console.log($codeid);
          info_url = url + '/'+codeid+'/edit';
          //console.log($info_url);
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
                        alert(d.message);
                        location.reload();
                    }
                },
                error:function(d){
                    console.log(d);
                }
            });
        });
      //Delete  
      function populateForm(data){
          $("#name").val(data.name);
          $("#surname").val(data.surname);
          $("#phone").val(data.phone);
          $("#email").val(data.email);
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