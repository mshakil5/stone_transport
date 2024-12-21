@extends('admin.layouts.admin')

@section('content')

<!-- Main content -->
<section class="content pt-3" id="newBtnSection">
  <div class="container-fluid">
    <div class="row">
      <div class="col-2">
        <button type="button" class="btn btn-secondary my-3" id="newBtn">Add new</button>
      </div>
    </div>
  </div>
</section>
<!-- /.content -->



<!-- Main content -->
<section class="content" id="addThisFormContainer">
  <div class="container-fluid">
    <div class="row justify-content-md-center">
      <!-- right column -->
      <div class="col-md-8">
        <!-- general form elements disabled -->
        <div class="card card-secondary">
          <div class="card-header">
            <h3 class="card-title">Add new ghat</h3>
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
                    <input type="text" class="form-control" id="name" name="name">
                  </div>
                </div>


                <div class="col-sm-6">
                  <div class="form-group">
                    <label>Code</label>
                    <input type="text" class="form-control" id="code" name="code">
                  </div>
                </div>

                <div class="col-sm-12">
                  <div class="form-group">
                    <label>Location</label>
                    <input type="text" class="form-control" id="location" name="location">
                  </div>
                </div>

                <div class="col-sm-12">
                  <div class="form-group">
                    <label>Description</label>
                    <input type="text" class="form-control" id="description" name="description">
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
                  <th>Name</th>
                  <th>Location</th>
                  <th>Description</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody>
                @foreach ($data as $key => $data)
                <tr>
                  <td>{{ $key + 1 }}</td>
                  <td>{{$data->name}}</td>
                  <td>{{$data->location}}</td>
                  <td>{{$data->description}}</td>
                  <td>
                    <a id="EditBtn" rid="{{$data->id}}"><i class="fa fa-edit" style="color: #2196f3;font-size:16px;"></i></a>
                    <a id="deleteBtn" rid="{{$data->id}}"><i class="fa fa-trash-o" style="color: red;font-size:16px;"></i></a>
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
    //header for csrf-token is must in laravel
    $.ajaxSetup({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
    });
    //
    var url = "{{URL::to('/admin/ghat')}}";
    var upurl = "{{URL::to('/admin/ghat-update')}}";
    // console.log(url);
    $("#addBtn").click(function() {
      //   alert("#addBtn");
      if ($(this).val() == 'Create') {
        var form_data = new FormData();
        form_data.append("name", $("#name").val());
        form_data.append("code", $("#code").val());
        form_data.append("location", $("#location").val());
        form_data.append("description", $("#description").val());
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

              $(".ermsg").html(d.message);
              window.setTimeout(function() {
                location.reload()
              }, 2000)
            }
          },
          error: function(d) {
            console.log(d);
          }
        });
      }
      //create  end
      //Update
      if ($(this).val() == 'Update') {
        var form_data = new FormData();
        form_data.append("name", $("#name").val());
        form_data.append("code", $("#code").val());
        form_data.append("location", $("#location").val());
        form_data.append("description", $("#description").val());
        form_data.append("codeid", $("#codeid").val());

        $.ajax({
          url: upurl,
          type: "POST",
          dataType: 'json',
          contentType: false,
          processData: false,
          data: form_data,
          success: function(d) {
            console.log(d);
            if (d.status == 303) {
              $(".ermsg").html(d.message);
              pagetop();
            } else if (d.status == 300) {
              $(".ermsg").html(d.message);
              window.setTimeout(function() {
                location.reload()
              }, 2000)
            }
          },
          error: function(d) {
            console.log(d);
          }
        });
      }
      //Update
    });
    //Edit
    $("#contentContainer").on('click', '#EditBtn', function() {
      //alert("btn work");
      codeid = $(this).attr('rid');
      //console.log($codeid);
      info_url = url + '/' + codeid + '/edit';
      //console.log($info_url);
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
            alert(d.message);
            location.reload();
          }
        },
        error: function(d) {
          console.log(d);
        }
      });
    });
    //Delete  
    function populateForm(data) {
      $("#name").val(data.name);
      $("#code").val(data.code);
      $("#location").val(data.location);
      $("#description").val(data.description);
      $("#codeid").val(data.id);
      $("#addBtn").val('Update');
      $("#addBtn").html('Update');
      $("#addThisFormContainer").show(300);
      $("#newBtn").hide(100);
    }

    function clearform() {
      $('#createThisForm')[0].reset();
      $("#addBtn").val('Create');
      $("#addBtn").html('Create');
    }
  });
</script>
@endsection