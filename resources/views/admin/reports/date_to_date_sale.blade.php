@extends('admin.layouts.admin')

@section('content')
<section class="content pt-3" id="contentContainer">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <a href="{{ route('reports.index') }}" class="btn btn-secondary mb-3">Back</a>
                <div class="card card-secondary">
                    <div class="card-header">
                        <h3 class="card-title">Date-to-Date Sales</h3>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label for="start-date" class="form-label">Start Date</label>
                                <input type="date" id="start-date" class="form-control" placeholder="Start Date">
                            </div>
                            <div class="col-md-4">
                                <label for="end-date" class="form-label">End Date</label>
                                <input type="date" id="end-date" class="form-control" placeholder="End Date">
                            </div>
                            <div class="col-md-4 d-flex align-items-end">
                                <button id="filter-button" class="btn btn-secondary me-2">Filter</button>
                                <button id="reset-button" class="btn btn-secondary mx-2"><i class="fas fa-sync-alt"></i></button>
                            </div>
                        </div>
                        <div class="alert alert-danger d-none" id="date-error">
                            Please select a start date.
                        </div>
                        <table id="date-to-date-sales-table" class="table table-bordered table-striped table-fluid">
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
        var table = $('#date-to-date-sales-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('reports.dateToDateSalesDataTable') }}",
                data: function (d) {
                    d.start_date = $('#start-date').val();
                    d.end_date = $('#end-date').val();
                }
            },
            pageLength: 50,
            columns: [
                { data: 'purchase_date', name: 'purchase_date' },
                { data: 'invoice', name: 'invoice' },
                { 
                    data: null,
                    render: function (data, type, row) {
                        return row.name + '<br>' + row.email + '<br>' + row.phone;
                    },
                    name: 'name'
                },
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

        $('#filter-button').click(function () {
            var startDate = $('#start-date').val();
            if (!startDate) {
                $('#date-error').removeClass('d-none');
                return;
            }
            $('#date-error').addClass('d-none');
            table.ajax.reload();
        });

        $('#reset-button').click(function () {
            $('#start-date').val('');
            $('#end-date').val('');
            table.ajax.reload();
        });
    });
</script>
@endsection
