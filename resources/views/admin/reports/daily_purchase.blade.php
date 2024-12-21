@extends('admin.layouts.admin')

@section('content')
<section class="content pt-3" id="contentContainer">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <a href="{{ route('reports.index') }}" class="btn btn-secondary mb-3">Back</a>
                <div class="card card-secondary">
                    <div class="card-header">
                        <h3 class="card-title">Daily Purchase</h3>
                    </div>
                    <div class="card-body">
                        <table id="daily-purchase-table" class="table table-bordered table-striped table-fluid">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Invoice</th>
                                    <th>Supplier</th>
                                    <th>Total Amount</th>
                                    <th>Paid Amount</th>
                                    <th>Due Amount</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Modal for Viewing Purchase Details -->
<div class="modal fade" id="viewPurchaseModal" tabindex="-1" aria-labelledby="viewPurchaseModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewPurchaseModalLabel">View Purchase Details</h5>
            </div>
            <div class="modal-body">
                <div class="row mb-3">
                    <div class="col"><strong>Date:</strong> <span id="purchaseDate"></span></div>
                    <div class="col"><strong>Invoice:</strong> <span id="purchaseInvoice"></span></div>
                </div>
                <div class="row mb-3">
                    <div class="col"><strong>Supplier:</strong> <span id="supplierName"></span></div>
                    <div class="col"><strong>Transaction Type:</strong> <span id="purchaseType"></span></div>
                </div>
                <div class="row mb-3">
                    <div class="col"><strong>Ref:</strong> <span id="purchaseRef"></span></div>
                    <div class="col"><strong>Total Amount:</strong> <span id="purchaseNetAmount"></span></div>
                </div>
                <div class="row mb-3">
                    <div class="col"><strong>Paid Amount:</strong> <span id="purchasePaidAmount"></span></div>
                    <div class="col"><strong>Due Amount:</strong> <span id="purchaseDueAmount"></span></div>
                </div>

                <div class="mb-3">
                    <h5>Purchase History</h5>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Product Name</th>
                                <th>Quantity</th>
                                <th>Price</th>
                                <th>Total Vat</th>
                                <th>Net Total</th>
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
@endsection

@section('script')
<script>
    $(function () {
        $('#daily-purchase-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('reports.dailyPurchasesDataTable') }}",
            pageLength: 50,
            columns: [
                { data: 'purchase_date', name: 'purchase_date' },
                { data: 'invoice', name: 'invoice' },
                { data: 'supplier.name', name: 'supplier.name' },
                { data: 'total_amount', name: 'total_amount' },
                { data: 'paid_amount', name: 'paid_amount' },
                { data: 'due_amount', name: 'due_amount' },
                {
                    data: 'id',
                    render: function (data, type, row) {
                        return '<a class="btn btn-sm btn-info" onclick="showViewPurchaseModal(' + data + ')"><i class="fas fa-eye"></i></a>';
                    },
                    orderable: false,
                    searchable: false
                }
            ]
        });
    });

    function showViewPurchaseModal(purchaseId) {
        $.ajax({
            url: '/admin/purchase/' + purchaseId + '/history',
            type: 'GET',
            success: function(response) {
                $('#purchaseDate').text(moment(response.purchase_date).format('DD-MM-YYYY'));
                $('#purchaseInvoice').text(response.invoice);
                $('#supplierName').text(response.supplier ? response.supplier.name : 'Unknown Supplier');
                $('#purchaseType').text(response.purchase_type);
                $('#purchaseRef').text(response.ref);
                $('#purchaseNetAmount').text(response.net_amount);
                $('#purchasePaidAmount').text(response.paid_amount);
                $('#purchaseDueAmount').text(response.due_amount);

                if (response.purchase_history && response.purchase_history.length > 0) {
                    let purchaseHistoryHtml = '';
                    response.purchase_history.forEach(function(history) {
                        purchaseHistoryHtml += `
                            <tr>
                                <td>${history.product.name}</td>
                                <td>${history.quantity}</td>
                                <td>${history.purchase_price}</td>
                                <td>${history.total_vat}</td>
                                <td>${history.total_amount_with_vat}</td>
                            </tr>`;
                    });

                    $('#purchaseHistoryTableBody').html(purchaseHistoryHtml);
                } else {
                    $('#purchaseHistoryTableBody').html('<tr><td colspan="5">No purchase history found.</td></tr>');
                }

                $('#viewPurchaseModal').modal('show');
            },
            error: function(xhr, status, error) {
                console.error(xhr.responseText);
            }
        });

        $(document).on('click', '[data-bs-dismiss="modal"]', function(event) {
            $('#viewPurchaseModal').modal('hide');
        });

    }
</script>
@endsection