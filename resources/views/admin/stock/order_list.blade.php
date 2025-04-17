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
                                    <th>Adv. Date</th>
                                    <th>Invoice</th>
                                    <th>Supplier</th>
                                    <th>Con. No</th>
                                    <th>M. Vessel</th>
                                    <th>Payment Type</th>
                                    <th>Adv. Amnt</th>
                                    <th>Adv. Qty</th>
                                    <th>Net Amnt</th>
                                    <th>Payments</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($data as $key => $purchase)
                                <tr>
                                    <td>{{ $key + 1 }}</td>
                                    <td>{{ \Illuminate\Support\Carbon::parse($purchase->advance_date)->format('d-m-Y') }}</td>
                                    <td>{{ $purchase->invoice ?? '' }}</td>
                                    <td>
                                        @if($purchase->supplier)
                                          {{ $purchase->supplier->name }}
                                          <br> {{ $purchase->supplier->email }}
                                          <br> {{ $purchase->supplier->phone }}
                                        @endif
                                    </td>
                                    <td>{{ $purchase->consignment_number }}</td>
                                    <td>{{ $purchase->motherVessel->name }}</td>
                                    <td>{{ $purchase->purchase_type }}</td>
                                    <td>{{ $purchase->advance_amount }}</td>
                                    <td>{{ $purchase->advance_quantity }}</td>
                                    <td>{{ $purchase->net_amount }}</td>
                                    <td>
                                        <button class="btn btn-sm btn-warning pay-btn mb-2" data-id="{{ $purchase->id }}">
                                            <i class="fas fa-money-bill-wave"></i> Advance Pay
                                        </button>
                                        <a href="{{ route('advance.transactions', $purchase->id) }}" class="btn btn-sm btn-info">
                                            <i class="fas fa-history"></i> Payment History
                                        </a>
                                    </td>
                                    <td>
                                        <a class="btn btn-sm btn-info mb-2" onclick="showViewPurchaseModal({{ $purchase->id }})">View Details
                                        </a>
                                        @if(in_array('9', json_decode(auth()->user()->role->permission)))
                                            <a href="{{ route('purchase.edit', $purchase->id) }}" class="btn btn-sm btn-info"></i> 
                                              @if($purchase->invoice)
                                                Update Stock
                                              @else 
                                                Add To Stock
                                              @endif
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
                  <button type="submit" class="btn btn-warning">Pay</button>
              </div>
          </form>
      </div>
  </div>
</div>

<div class="modal fade" id="viewPurchaseModal" tabindex="-1" aria-labelledby="viewPurchaseModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
      <div class="modal-content">
          <div class="modal-header">
              <h5 class="modal-title" id="viewPurchaseModalLabel">Order Details</h5>
          </div>
          <div class="modal-body">
              <div class="row mb-3">
                  <div class="col"><strong>Advance Date:</strong> <span id="advanceDate"></span></div>
                  <div class="col"><strong>Receiving Date:</strong> <span id="receivingDate"></span></div>
              </div>
              <div class="row mb-3">
                  <div class="col"><strong>Mother Vessel:</strong> <span id="motherVessel"></span></div>
                  <div class="col"><strong>Payment Type:</strong> <span id="paymentType"></span></div>
              </div>
              <div class="row mb-3">
                  <div class="col"><strong>Supplier:</strong> <span id="supplierName"></span></div>
                  <div class="col"><strong>Bill Number:</strong> <span id="billNumber"></span></div>
              </div>
              <div class="row mb-3">
                  <div class="col"><strong>Total Advance Amount:</strong> <span id="totalAdvanceAmount"></span></div>
                  <div class="col"><strong>Advance Qty:</strong> <span id="totalAdvanceQty"></span></div>
              </div>
              <div class="row mb-3">
                <div class="col"><strong>Total Unloading Cost:</strong> <span id="unloadingCost"></span></div>
                <div class="col"><strong>Total Lighter Rent:</strong> <span id="lighterRent"></span></div>
              </div>
              <div class="row mb-3">
                  <div class="col"><strong>VAT(%):</strong> <span id="vatPercent"></span></div>
                  <div class="col"><strong>Total VAT Amount:</strong> <span id="totalVatAmount"></span></div>
              </div>
              <div class="row mb-3">
                  <div class="col"><strong>Discount Amount:</strong> <span id="discountAmount"></span></div>
                  <div class="col"><strong>Net Amount:</strong> <span id="netAmount"></span></div>
              </div>
          
              <div class="mb-3">
                  <h5>Product History</h5>
                  <table class="table table-bordered">
                      <thead>
                          <tr>
                              <th>Product</th>
                              <th>L. Vessel</th>
                              <th>Warehouse</th>
                              <th>Ghat</th>
                              <th>Qty</th>
                              <th>Unit</th>
                              <th>Total</th>
                          </tr>
                      </thead>
                      <tbody id="purchaseHistoryTableBody">
                      </tbody>
                  </table>
              </div>
          </div>
          <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
          </div>
      </div>
  </div>
</div>

<script>
  function showViewPurchaseModal(purchaseId) {
      $.ajax({
          url: '/admin/purchase/' + purchaseId + '/history',
          type: 'GET',
          success: function(response) {

              $('#advanceDate').text(moment(response.advance_date).format('DD-MM-YYYY'));
              $('#receivingDate').text(moment(response.receiving_date).format('DD-MM-YYYY'));
              $('#motherVessel').text(response.mother_vessel.name);
              $('#paymentType').text(response.purchase_type);
              $('#supplierName').text(response.supplier ? response.supplier.name : 'Unknown Supplier');
              $('#billNumber').text(response.bill_number);
              $('#totalAdvanceAmount').text(response.advance_amount);
              $('#discountAmount').text(response.discount);
              $('#totalAdvanceQty').text(response.advance_quantity);
              $('#vatPercent').text(response.vat_percent);
              $('#totalVatAmount').text(response.total_vat_amount);
              $('#unloadingCost').text(response.total_unloading_cost);
              $('#lighterRent').text(response.total_lighter_rent);
              $('#netAmount').text(response.net_amount);
              if(response.invoice){
                $('.modal-title').text(response.invoice + ' Order Details');
              } else{
                $('.modal-title').text('Order Details');
              }

              // Purchase history
              if (response.purchase_history && response.purchase_history.length > 0) {
                  let purchaseHistoryHtml = '';
                  response.purchase_history.forEach(function(item) {
                      purchaseHistoryHtml += `
                          <tr>
                              <td>${item.product?.name || ''}</td>
                              <td>${item.lighter_vessel?.name || ''}</td>
                              <td>${item.warehouse?.name || ''}</td>
                              <td>${item.ghat?.name || ''}</td>
                              <td>${item.quantity}</td>
                              <td>${item.purchase_price}</td>
                              <td>${item.total_amount}</td>
                          </tr>`;
                  });
                  $('#purchaseHistoryTableBody').html(purchaseHistoryHtml);
              } else {
                  $('#purchaseHistoryTableBody').html('<tr><td colspan="8">No data found.</td></tr>');
              }

              $('#viewPurchaseModal').modal('show');
          },
          error: function(xhr, status, error) {
              console.log(xhr.responseText);
          }
      });

      $(document).on('click', '[data-bs-dismiss="modal"]', function(event) {
          $('#viewPurchaseModal').modal('hide');
      });
  }
</script>

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
