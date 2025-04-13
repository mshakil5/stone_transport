@extends('admin.layouts.admin')

@section('content')

<section class="content pt-3" id="contentContainer">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card card-secondary">
                    <div class="card-header">
                        <h3 class="card-title">Order List</h3>
                    </div>
                    <div class="card-body">
                        <table id="example1" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Sl</th>
                                    <th>Advance Date</th>
                                    <th>Consignment Number</th>
                                    <th>Mother Vessel</th>
                                    <th>Payment Type</th>
                                    <th>Advance Amount</th>
                                    <th>Advance Quantity</th>
                                    <th>Advance Pay</th>
                                    <th>Purchase</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($data as $key => $order)
                                <tr>
                                    <td>{{ $key + 1 }}</td>
                                    <td>{{ \Illuminate\Support\Carbon::parse($order->advance_date)->format('d-m-Y') }}</td>
                                    <td>{{ $order->consignment_number }}</td>
                                    <td>{{ $order->motherVessel->name }}</td>
                                    <td>{{ $order->purchase_type }}</td>
                                    <td>{{ $order->advance_amount }}</td>
                                    <td>{{ $order->advance_quantity }}</td>
                                    <td>
                                        <button class="btn btn-sm btn-warning pay-btn" data-id="{{ $order->id }}">Advance Pay</button>
                                    </td>
                                    <td>
                                      @if(in_array('9', json_decode(auth()->user()->role->permission)))
                                        <a href="{{ route('purchase.edit', $order->id) }}" class="btn btn-sm btn-info">
                                            Add To Stock
                                        </a>
                                      @endif
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

<div class="modal fade" id="payModal" tabindex="-1" role="dialog" aria-labelledby="payModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
      <div class="modal-content">
          <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
              </button>
          </div>
          <form id="payForm">
              <div class="modal-body">
                  <div class="form-group">
                      <label for="paymentAmount">Payment Amount</label>
                      <input type="number" class="form-control" id="paymentAmount" name="paymentAmount" placeholder="Enter payment amount" min="1">
                  </div>

                  <div class="form-group">
                      <label for="document">Document</label>
                      <input type="file" class="form-control-file" id="document" name="document">
                  </div>

                  <div class="form-group">
                      <label for="payment_type">Payment Type</label>
                      <select name="payment_type" id="payment_type" class="form-control">
                          <option value="Cash">Cash</option>
                          <option value="Bank">Bank</option>
                          <option value="Due">Due</option>
                      </select>
                  </div>

                  <div class="form-group">
                      <label for="paymentNote">Payment Note</label>
                      <textarea class="form-control" id="paymentNote" name="paymentNote" rows="3" placeholder="Enter payment note"></textarea>
                  </div>
              </div>
              <div class="modal-footer">
                  <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                  <button type="submit" class="btn btn-warning">Receive</button>
              </div>
          </form>
      </div>
  </div>
</div>

@endsection

@section('script')

<script>
  $(document).ready(function () {
      $(document).on('click', '.pay-btn', function () {
          const orderId = $(this).data('id');
          $('#payForm').data('order-id', orderId);
          $('#payModal').modal('show');
      });

      $('#payForm').on('submit', function (e) {
          e.preventDefault();

          const paymentAmount = parseFloat($('#paymentAmount').val());
          if (isNaN(paymentAmount) || paymentAmount <= 0) {
              alert('Please enter a valid positive amount.');
              return;
          }

          const formData = new FormData(this);
          formData.append('order_id', $(this).data('order-id'));
          formData.set('paymentAmount', Math.abs(paymentAmount));

          const loader = $('<div class="loader">Processing...</div>').appendTo('body');

          $.ajax({
              url: '{{ URL::to('/admin/advance-payment') }}',
              method: 'POST',
              data: formData,
              processData: false,
              contentType: false,
              headers: {
                  'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
              },
              success: function (response) {
                  $('#payModal').modal('hide');
                  $('#payForm')[0].reset();

                  swal({
                      text: "Payment stored successfully",
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
                  console.error(xhr.responseText);
                  alert('An error occurred: ' + xhr.responseText);
              },
              complete: function () {
                  loader.remove();
              }
          });
      });
  });
</script>

<script>
    $(function () {
      $("#example1").DataTable({
        "responsive": true, 
        "lengthChange": false, 
        "autoWidth": false,
        "buttons": ["copy", "csv", "excel", "pdf", "print"]
      }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
    });
</script>

@endsection
