@extends('admin.layouts.admin')

@section('content')

<section class="content pt-3" id="contentContainer">
    <div class="container-fluid">
      <div class="row">
        <div class="col-12">
          <!-- /.card -->

          <div class="mb-3">
            <a href="{{ route('allsupplier') }}" class="btn btn-secondary">
              <i class="fa fa-arrow-left"></i> Back
            </a>
          </div>

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
                  <th>Product</th>
                  <th>Price</th>
                  <th>Quantity</th>
                  <th>Description</th>
                  <th>Approved</th>
                </tr>
                </thead>
                <tbody>
                  @foreach ($data as $key => $data)
                  <tr>
                    <td>{{ $key + 1 }}</td>
                    <td>{{ $data->product->name ?? 'N/A' }}</td>
                    <td>{{ $data->price ?? 'N/A' }}</td>
                    <td>{{ $data->quantity ?? 'N/A' }}</td>
                    <td>{!! $data->description !!}</td>
                    <td>
                      <input type="checkbox" class="approve-checkbox" data-id="{{ $data->id }}" {{ $data->is_approved ? 'checked' : '' }}>
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
  $(document).ready(function() {
      $('.approve-checkbox').on('change', function() {
          var itemId = $(this).data('id');
          var isApproved = $(this).is(':checked') ? 1 : 0;

          $.ajax({
              url: '/admin/approve-supplier-products',
              method: 'POST',
              data: {
                  _token: '{{ csrf_token() }}',
                  id: itemId,
                  is_approved: isApproved
              },
              success: function(response) {
                  alert(response.message);
              },
              error: function(xhr) {
                  console.error(xhr.responseText);
              }
          });
      });
  });
</script>

@endsection