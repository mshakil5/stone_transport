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
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label for="advance_date">Advance Date <span class="text-danger">*</span></label>
                                        <input type="date" class="form-control" id="advance_date" name="advance_date" required value="{{ date('Y-m-d') }}">
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label for="consignment_number">Consignment Number <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="consignment_number" name="consignment_number" required>
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label for="mother_vassels_id">Mother Vessel <span class="text-danger">*</span>
                                          <span class="badge badge-success" style="cursor: pointer;" data-toggle="modal" data-target="#newMotherVesselModal">
                                              Add New
                                          </span>
                                        </label>
                                        <select class="form-control select2" id="mother_vassels_id" name="mother_vassels_id" required>
                                            <option value="">Select...</option>
                                            @foreach($motherVassels as $mother_vassel)
                                                <option value="{{ $mother_vassel->id }}">{{ $mother_vassel->name }} - {{ $mother_vassel->code }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label for="supplier_id">Supplier <span class="text-danger">*</span>
                                          <span class="badge badge-success" style="cursor: pointer;" data-toggle="modal" data-target="#newSupplierModal">
                                              Add New
                                          </span>
                                        </label>
                                        <select class="form-control select2" id="supplier_id" name="supplier_id" required>
                                            <option value="">Select...</option>
                                            @foreach($suppliers as $supplier)
                                                <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label for="purchase_type">Payment Type <span class="text-danger">*</span></label>
                                        <select class="form-control" id="purchase_type" name="purchase_type" required>
                                            <option value="">Select...</option>
                                            <option value="Cash">Cash</option>
                                            <option value="Bank">Bank</option>
                                            <option value="Due">Due</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label for="advance_amount">Advance Amount</label>
                                        <input type="number" step="0.01" class="form-control" id="advance_amount" name="advance_amount" placeholder="">
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label for="advance_quantity">Advance Quantity</label>
                                        <input type="number" class="form-control" id="advance_quantity" name="advance_quantity" min="1">
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label for="cost_per_unit">Cost Per Unit</label>
                                        <input type="number" class="form-control" id="cost_per_unit" name="cost_per_unit" min="1">
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

@include('admin.inc.modal.mother_vessel_modal')
@include('admin.inc.modal.supplier_modal')

@endsection

@section('script')
<script>
  $(document).ready(function() {

      $('#saveSupplierBtn').on('click', function() {

        let password = $('#password').val();
        let confirmPassword = $('#confirm_password').val();
        let name = $('#supplier_name').val();
        let email = $('#supplier_email').val();

        if (name == '') {
          swal({
            text: "Name is required !",
            icon: "error",
            button: {
                text: "OK",
                className: "swal-button--confirm"
            }
          })

          return false;
        }

        if (password !== confirmPassword) {
            
            swal({
                text: "Passwords do not match !",
                icon: "error",
                button: {
                    text: "OK",
                    className: "swal-button--confirm"
                }
            });

            return false;
        }

        let formData = {
            id_number: $('#supplier_id_number').val(),
            name: $('#supplier_name').val(),
            email: $('#supplier_email').val(),
            phone: $('#supplier_phone').val(),
            password: $('#password').val(),
            vat_reg: $('#vat_reg1').val(),
            contract_date: $('#contract_date').val(),
            address: $('#address').val(),
            company: $('#company').val(),
            _token: '{{ csrf_token() }}'
        };

        $.ajax({
            url: '{{ route('supplier.store') }}',
            type: 'POST',
            data: formData,
            success: function(response) {
                if (response.success) {
                    let newOption = new Option(response.data.name, response.data.id, false, true);
                    $('#supplier_id').append(newOption).trigger('change');
                    $('#newSupplierModal').modal('hide');
                    $('#newSupplierForm')[0].reset();
                    swal({
                        text: "Created successfully",
                        icon: "success",
                        button: {
                            text: "OK",
                            className: "swal-button--confirm"
                        }
                    });
                } else {
                    alert('Failed to add supplier.');
                }
            },
            error: function(xhr, status, error) {
                  var errorMessage = "An error occurred. Please try again later.";
                    if (xhr.responseJSON && xhr.responseJSON.errors) {
                        errorMessage = Object.values(xhr.responseJSON.errors)[0][0];
                    }
                  swal({
                      text: errorMessage,
                      icon: "error",
                      button: {
                          text: "OK",
                          className: "swal-button--confirm"
                      }
                  })
                console.log(xhr.responseText);
            }
        });
      });

      $('#saveMotherVesselBtn').on('click', function () {
          let formData = {
              name: $('#name').val(),
              code: $('#code').val(),
              description: $('#description').val(),
              _token: '{{ csrf_token() }}'
          };

          $.ajax({
              url: '/admin/mother-vassel',
              type: 'POST',
              data: formData,
              success: function (response) {
                  if (response.status === 300) {
                    let newOption = new Option(`${response.data.name} - ${response.data.code ?? ''}`, response.data.id, true, true);
                    $('#mother_vassels_id').append(newOption).trigger('change');

                      $('#newMotherVesselModal').modal('hide');
                      $('#motherVesselForm')[0].reset();
                      swal({
                          text: "Mother Vessel created successfully",
                          icon: "success",
                          button: {
                              text: "OK",
                              className: "swal-button--confirm"
                          }
                      });
                  } else {
                      swal({
                          html: true,
                          text: $(response.message).text(),
                          icon: "warning"
                      });
                  }
              },
              error: function (xhr) {
                  alert('Server Error. Try again.');
              }
          });
      });

      $('#createThisForm').on('submit', function(e) {
          e.preventDefault();
          var formData = $(this).serialize();

          $.ajax({
              url: '{{ route("storeOrder") }}',
              type: 'POST',
              data: formData,
              success: function(response) {
                  swal("Success!", "Order created successfully!", "success").then(() => {
                      window.location.href = "{{ route('orderList') }}";
                  });
              },
              error: function(xhr, status, error) {
                  console.error(xhr.responseText);
                  var errorMessage = xhr.responseJSON.message || "An error occurred. Please try again.";
                  swal("Error!", errorMessage, "error");
              }
          });
      });

      $('#advance_amount, #advance_quantity, #cost_per_unit').on('input', function() {
          if ($(this).val() < 0) {
              $(this).val(0);
          }
      });
  });
</script>
@endsection