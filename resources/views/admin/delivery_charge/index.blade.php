@extends('admin.layouts.admin')

@section('content')

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
                                    <div class="col-sm-12">
                                        <div class="form-group">
                                            <label>Min Price<span style="color: red;">*</span></label>
                                            <input type="number" class="form-control" id="min_price" name="min_price" placeholder="Enter min price">
                                        </div>
                                    </div>
                                    <div class="col-sm-12">
                                        <div class="form-group">
                                            <label>Max Price</label>
                                            <input type="number" class="form-control" id="max_price" name="max_price" placeholder="Enter max price">
                                        </div>
                                    </div>
                                    <div class="col-sm-12">
                                        <div class="form-group">
                                            <label>Delivery Charge<span style="color: red;">*</span></label>
                                            <input type="number" class="form-control" id="delivery_charge" name="delivery_charge" placeholder="Enter delivery charge">
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
                                        <th>Min Price</th>                          
                                        <th>Max Price</th>                          
                                        <th>Delivery Charge</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($data as $key => $item)
                                        <tr>
                                            <td>{{ $key + 1 }}</td>
                                            <td>{{ $item->min_price }}</td>
                                            <td>{{ $item->max_price }}</td>
                                            <td>{{ $item->delivery_charge }}</td>
                                            <td>
                                                <a id="EditBtn" rid="{{ $item->id }}">
                                                    <i class="fa fa-edit" style="color: #2196f3; font-size:16px;"></i>
                                                </a>
                                                <a id="deleteBtn" rid="{{ $item->id }}">
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
      var url = "{{URL::to('/admin/delivery-charge')}}";
      var upurl = "{{URL::to('/admin/delivery-charge-update')}}";

      $("#addBtn").click(function(){

          //create
          if($(this).val() == 'Create') {
              var form_data = new FormData();
              form_data.append("min_price", $("#min_price").val());
              form_data.append("max_price", $("#max_price").val());
              form_data.append("delivery_charge", $("#delivery_charge").val());

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
              form_data.append("min_price", $("#min_price").val());
              form_data.append("max_price", $("#max_price").val());
              form_data.append("delivery_charge", $("#delivery_charge").val());

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
          $("#min_price").val(data.min_price);
          $("#max_price").val(data.max_price);
          $("#delivery_charge").val(data.delivery_charge);
          $("#codeid").val(data.id);
          $("#addBtn").val('Update');
          $("#addBtn").html('Update');
          $("#addThisFormContainer").show(300);
          $("#newBtn").hide(100);
          $("#cardTitle").text('Update this data');
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