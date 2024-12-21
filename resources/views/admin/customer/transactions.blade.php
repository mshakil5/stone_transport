@extends('admin.layouts.admin')

@section('content')

<section class="content pt-3" id="contentContainer">
    <div class="container-fluid">
      <div class="row">
        <div class="col-12">
          <!-- /.card -->

          <div class="mb-3">
            <a href="{{ route('allcustomer') }}" class="btn btn-secondary">
              <i class="fa fa-arrow-left"></i> Back
            </a>
          </div>

          <div class="card card-secondary">
            <div class="card-header">
              <h3 class="card-title">{{$customer->name}} All Transactions</h3>
            </div>
            <!-- /.card-header -->
            <div class="card-body">

              <div class="text-center mb-4 company-name-container">
                  <h2>{{$customer->name}}</h2>

                      <h4>WholeSaler transaction history</h4>
              </div>



              <table id="example1" class="table table-bordered table-striped">
                <thead>
                <tr>
                  <th>Sl</th>
                  <th>Date</th>
                  <th>Description</th>
                  <th>Payment Type</th>
                  <th>Document</th>
                  <th>Dr Amount</th>
                  <th>Cr Amount</th>
                  <th>Balance</th>
                  <th>Note</th>
                  <th>Action</th>
                </tr>
                </thead>
                <tbody>

                  @php
                      $balance = $totalBalance;
                  @endphp


                  @foreach ($transactions as $key => $data)
                  <tr>
                    <td>{{ $key + 1 }}</td>
                   <td>{{ \Carbon\Carbon::parse($data->date)->format('d-m-Y') }}</td>
                    <td>
                    @if(in_array($data->payment_type, ['Cash', 'Bank']))
                      Received
                    @elseif (in_array($data->payment_type, ['Return']))
                      Return
                    @else
                      Sales
                    @endif
                    </td>
                    <td>{{ $data->payment_type }}</td>
                    <td>
                        @if($data->document)
                            <a href="{{ asset($data->document) }}" target="_blank" class="btn btn-secondary">
                                View
                            </a>
                        @else
                            Not available
                        @endif
                    </td>

                    @if(in_array($data->payment_type, ['Credit']))
                      <td style="text-align: right">{{ number_format($data->at_amount, 2) }}</td>
                      <td></td>
                      <td style="text-align: right">{{ number_format($balance, 2) }}</td>
                      @php
                          $balance = $balance - $data->at_amount;
                      @endphp
                    @elseif(in_array($data->payment_type, ['Cash', 'Bank', 'Return']))
                      <td></td>
                      <td style="text-align: right">{{ number_format($data->at_amount, 2) }}</td>
                      <td style="text-align: right">{{ number_format($balance, 2) }}</td>
                      @php
                          $balance = $balance + $data->at_amount;
                      @endphp
                    @endif
                    <td>{!! $data->note !!}</td>
                    <td>
                      @if($data->payment_type !== 'Credit')
                      <i class="fas fa-edit edit-btn" 
                        data-id="{{ $data->id }}" 
                        data-at-amount="{{ $data->at_amount }}" 
                        data-note="{{ $data->note }}" 
                        data-document="{{ $data->document }}" 
                        style="cursor: pointer;">
                      </i>
                      @endif 
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

<!-- Modal Structure -->
<div class="modal fade" id="editTransactionModal" tabindex="-1" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="editModalLabel">Edit Transaction</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form id="editTransactionForm">
          <input type="hidden" id="transactionId" name="transactionId">

          <div class="form-group">
            <label for="editAtAmount">Amount</label>
            <input type="number" class="form-control" id="editAtAmount" name="at_amount" required>
          </div>

          <div class="form-group">
            <label for="editNote">Note</label>
            <textarea class="form-control" id="editNote" name="note" rows="3"></textarea>
          </div>

          <div class="form-group">
            <label for="editDocument">Document</label>
            <input type="file" class="form-control" id="editDocument" name="document">
          </div>

          <button type="button" class="btn btn-secondary" id="updateTransactionBtn">Update</button>
        </form>
      </div>
    </div>
  </div>
</div>

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
      $('.edit-btn').on('click', function () {
          var transactionId = $(this).data('id');
          var atAmount = $(this).data('at-amount');
          var note = $(this).data('note');
          var document = $(this).data('document');

          $('#transactionId').val(transactionId);
          $('#editAtAmount').val(atAmount);
          $('#editNote').val(note);

          $('#editTransactionModal').modal('show');
      });

      $('#updateTransactionBtn').on('click', function () {
          var formData = new FormData($('#editTransactionForm')[0]);

          // console.log(formData);
          $.ajax({
              url: '/admin/whole-saler/transactions/update',
              method: 'POST',
              data: formData,
              processData: false,
              contentType: false,
              headers: {
                  'X-CSRF-TOKEN': '{{ csrf_token() }}'
              },
              success: function (response) {
                  if (response.success) {
                      $('#editTransactionModal').modal('hide');
                      alert('Transaction updated successfully!');
                      location.reload();
                  } else {
                      alert('Failed to update transaction.');
                  }
              },
              error: function (xhr, status, error) {
                  console.log(error);
                  alert('Something went wrong!');
              }
          });
      });
  });

</script>

@endsection