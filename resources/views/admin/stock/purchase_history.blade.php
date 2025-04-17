@extends('admin.layouts.admin')

@section('content')

<section class="content pt-3" id="contentContainer">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card card-secondary">
                    <div class="card-header">
                        <h3 class="card-title">All Stocks</h3>
                    </div>
                    <div class="card-body">
                        <table id="example1" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Sl</th>
                                    <th>Date</th>
                                    <th>Invoice</th>
                                    <th>Supplier</th>
                                    <th>Net Amount</th>
                                    {{-- <th>Paid Amount</th>
                                    <th>Due Amount</th>
                                    <th>Not Transferred Quantity</th>
                                    <th>Missing Quantity</th>
                                    <th>Status</th> --}}
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($purchases as $key => $purchase)
                                <tr>
                                    <td>{{ $key + 1 }}</td>
                                    <td>{{ \Carbon\Carbon::parse($purchase->purchase_date)->format('d-m-Y') }}</td>
                                    <td>{{ $purchase->invoice }}</td>
                                    <td>
                                        @if($purchase->supplier)
                                        {{ $purchase->supplier->name }}
                                        <br> {{ $purchase->supplier->email }}
                                        <br> {{ $purchase->supplier->phone }}
                                        @endif
                                    </td>
                                    {{-- <td>{{ $purchase->ref }}</td> --}}
                                    <td>{{ $purchase->net_amount }}</td>
                                    {{-- <td>{{ $purchase->paid_amount }}</td>
                                    <td>
                                        <span  class="btn btn-sm btn-danger">£{{$purchase->due_amount }}</span>

                                        @if(in_array('12', json_decode(auth()->user()->role->permission)))
                                        <button class="btn btn-sm btn-warning pay-btn" 
                                            data-id="{{ $purchase->id }}" 
                                            @if($purchase->supplier)
                                            data-supplier-id="{{ $purchase->supplier->id }}" 
                                            @endif
                                            data-toggle="modal" 
                                            data-target="#payModal">
                                            Pay
                                        </button>
                                        @endif

                                    </td>
                                    @php
                                    $totalRemainingQuantity = $purchase->purchaseHistory->sum('remaining_product_quantity');
                                    $totalPurchasedQuantity = $purchase->purchaseHistory->sum('quantity');
                                    $totalReturnedQuantity = \App\Models\PurchaseReturn::whereIn('purchase_history_id', $purchase->purchaseHistory->pluck('id'))->sum('return_quantity');
                                    $totalNotReturnedQuantity = $totalPurchasedQuantity - $totalReturnedQuantity;
                                    @endphp

                                    <td>{{ $totalRemainingQuantity }}</td>
                                    <td>{{$purchase->purchaseHistory->sum('missing_product_quantity')}}</td>
                                    <td style="width: 100%;">
                                        <select class="form-control purchase-status" data-purchase-id="{{ $purchase->id }}">
                                            <option value="1" {{ $purchase->status == 1 ? 'selected' : '' }}>Processing</option>
                                            <option value="2" {{ $purchase->status == 2 ? 'selected' : '' }}>On The Way</option>
                                            <option value="3" {{ $purchase->status == 3 ? 'selected' : '' }}>Customs</option>
                                            <option value="4" {{ $purchase->status == 4 ? 'selected' : '' }}>Received</option>
                                        </select>
                                    </td> --}}
                                    <td>
                                        <a class="btn btn-sm btn-info" onclick="showViewPurchaseModal({{ $purchase->id }})">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        {{-- @if($purchase->status == 1) --}}
                                        <a href="{{ route('purchase.edit', $purchase->id) }}" class="btn btn-sm btn-primary">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        {{-- @endif --}}
                                        {{-- @if($purchase->status == 4 && $totalNotReturnedQuantity > 0)
                                        <a href="{{ route('returnProduct', $purchase->id) }}" class="btn btn-sm btn-warning">
                                            <i class="fas fa-undo-alt"></i>
                                        </a>
                                        @endif --}}
                                        {{-- @if ($totalRemainingQuantity > 1 && $purchase->status == 4)
                                            <a href="{{ route('transferToWarehouse', $purchase->id) }}" class="btn btn-sm btn-success">
                                                <i class="fas fa-arrow-right"></i>
                                            </a>
                                            
                                            <a href="{{ route('missingProduct', $purchase->id) }}" class="btn btn-sm btn-danger">
                                                <i class="fas fa-arrow-right"></i>
                                            </a>
                                         @endif    --}}
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

<!-- Modal -->
<div class="modal fade" id="viewPurchaseModal" tabindex="-1" aria-labelledby="viewPurchaseModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewPurchaseModalLabel">View Purchase Details</h5>
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
                    <h5>Purchase History</h5>
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

<div class="modal fade" id="payModal" tabindex="-1" role="dialog" aria-labelledby="payModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="payModalLabel">Supplier Payment Form</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="payForm">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="paymentAmount">Payment Amount <span style="color: red;">*</span></label>
                        <input type="number" class="form-control" id="paymentAmount" name="paymentAmount" placeholder="Enter payment amount">
                    </div>
                    
                    <div class="form-group">
                        <label for="document">Document</label>
                        <input type="file" class="form-control-file" id="document" name="document">
                    </div>

                    <div class="form-group">
                        <label for="payment_type">Payment Type <span style="color: red;">*</span></label>
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
    $(document).ready(function() {
        $('.pay-btn').click(function() {
            var purchaseId = $(this).data('id');
            var supplierId = $(this).data('supplier-id');

            $('#payForm').data('purchase-id', purchaseId);
            $('#payForm').data('supplier-id', supplierId);

            $('#paymentAmount').val('');
            $('#document').val('');
            $('#payment_type').val('Cash');
            $('#paymentNote').val('');
        });

        $('#payForm').submit(function(e) {
            e.preventDefault();

            var purchaseId = $(this).data('purchase-id');
            var supplierId = $(this).data('supplier-id');
            var paymentAmount = $('#paymentAmount').val();
            var document = $('#document')[0].files[0];
            var paymentType = $('#payment_type').val();
            var paymentNote = $('#paymentNote').val();

            var formData = new FormData();
            formData.append('purchase_id', purchaseId);
            formData.append('supplier_id', supplierId);
            formData.append('payment_amount', paymentAmount);
            formData.append('document', document);
            formData.append('payment_type', paymentType);
            formData.append('payment_note', paymentNote);

            // for (var pair of formData.entries()) {
            //     console.log(pair[0] + ': ' + pair[1]);
            // }

            $.ajax({
                url: '{{ URL::to('/admin/due-pay') }}',
                type: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
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

<script>
    function showViewPurchaseModal(purchaseId) {
        $.ajax({
            url: '/admin/purchase/' + purchaseId + '/history',
            type: 'GET',
            success: function(response) {
                console.log(response);

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
                    $('#purchaseHistoryTableBody').html('<tr><td colspan="8">No purchase history found.</td></tr>');
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

<script>
    $(document).on('change', '.purchase-status', function() {
        const purchaseId = $(this).data('purchase-id');
        const status = $(this).val();

        $.ajax({
            url: '/admin/purchases/update-status',
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                purchase_id: purchaseId,
                status: status
            },
            success: function(response) {
                swal({
                    text: "Status Changed",
                    icon: "success",
                    button: {
                        text: "OK",
                        className: "swal-button--confirm"
                    }
                }).then(() => {
                    location.reload();
                });
            },
            error: function(xhr, status, error) {
                console.log(xhr.responseText);
                swal({
                    text: "An error occurred while changing the status.",
                    icon: "error",
                    button: {
                        text: "OK",
                        className: "swal-button--confirm"
                    }
                });
            }
        });
    });
</script>

@endsection