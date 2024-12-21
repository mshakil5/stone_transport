@extends('admin.layouts.admin')

@section('content')
<section class="content pt-3" id="contentContainer">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <a href="{{ route('reports.index') }}" class="btn btn-secondary mb-3">Back</a>
                <div class="card card-secondary">
                    <div class="card-header">
                        <h3 class="card-title">Daily Sales</h3>
                    </div>
                    <div class="card-body">
                        <table id="monthly-sales-table" class="table table-bordered table-striped table-fluid">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Invoice</th>
                                    <th>Name, Email, Phone</th>
                                    <th>Subtotal</th>
                                    <th>Shipping</th>
                                    <th>Discount</th>
                                    <th>Total</th>
                                    <th>Payment Method</th>
                                    <th>Status</th>
                                    <th>Details</th>
                                </tr>
                            </thead>
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
        $('#monthly-sales-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('reports.dailySalesDataTable') }}",
            pageLength: 50,
            columns: [
                { data: 'purchase_date', name: 'purchase_date' },
                { data: 'invoice', name: 'invoice' },
                { data: 'user_details', name: 'user_detailsy' },
                { data: 'subtotal_amount', name: 'subtotal_amount' },
                { data: 'shipping_amount', name: 'shipping_amount' },
                { data: 'discount_amount', name: 'discount_amount' },
                { data: 'net_amount', name: 'net_amount' },
                { data: 'payment_method', name: 'payment_method' },
                { data: 'status', name: 'status' },
                { data: 'action', name: 'action', orderable: false, searchable: false }
            ],
            dom: 'Bfrtip',
            buttons: [
                'copy', 'csv', 'excel', 'pdf',
                {
                    extend: 'print',
                    text: 'Print',
                    exportOptions: {
                        columns: ':visible'
                    }
                }
            ]
        });
    });
</script>
@endsection
