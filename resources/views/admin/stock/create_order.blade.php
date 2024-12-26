@extends('admin.layouts.admin')

@section('content')

<section class="content pt-3" id="addThisFormContainer">
    <div class="container-fluid">
        <div class="row justify-content-md-center">
            <div class="col-md-12">
                <div class="card card-secondary">
                    <div class="card-header">
                        <h3 class="card-title" id="cardTitle">Create Order</h3>
                    </div>
                    <div class="card-body">
                        <form id="createThisForm">
                            @csrf
                            <div class="row">
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label for="advance_date">Advance Date <span class="text-danger">*</span></label>
                                        <input type="date" class="form-control" id="advance_date" name="advance_date" required>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label for="consignment_number">Consignment Number <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="consignment_number" name="consignment_number" placeholder="Enter consignment number" required>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label for="mother_vassels_id">Mother Vessel <span class="text-danger">*</span></label>
                                        <select class="form-control" id="mother_vassels_id" name="mother_vassels_id" required>
                                            <option value="">Select...</option>
                                            @foreach($motherVassels as $mother_vassel)
                                                <option value="{{ $mother_vassel->id }}">{{ $mother_vassel->name }} - {{ $mother_vassel->code }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label for="purchase_type">Payment Type <span class="text-danger">*</span></label>
                                        <select class="form-control" id="purchase_type" name="purchase_type" required>
                                            <option value="">Select...</option>
                                            <option value="Cash">Cash</option>
                                            <option value="Bank">Bank</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label for="advance_amount">Advance Amount <span class="text-danger">*</span></label>
                                        <input type="number" step="0.01" class="form-control" id="advance_amount" name="advance_amount" placeholder="Enter advance amount" required>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label for="advance_quantity">Advance Quantity <span class="text-danger">*</span></label>
                                        <input type="number" class="form-control" id="advance_quantity" name="advance_quantity" placeholder="Enter advance_quantity" min="1" required>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer">
                                <button type="submit" id="addBtn" class="btn btn-secondary" value="Create"><i class="fas fa-plus"></i> Create</button>
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
  $(document).ready(function() {
      $('#createThisForm').on('submit', function(e) {
          e.preventDefault();
          var formData = $(this).serialize();

          $.ajax({
              url: '{{ route("storeOrder") }}',
              type: 'POST',
              data: formData,
              success: function(response) {
                  swal("Success!", "Order created successfully!", "success")
                      .then((value) => {
                          $('#createThisForm')[0].reset();
                      });
              },
              error: function(xhr, status, error) {
                  console.error(xhr.responseText);
                  var errorMessage = xhr.responseJSON.message || "An error occurred. Please try again.";
                  swal("Error!", errorMessage, "error");
              }
          });
      });
  });
</script>
@endsection