@extends('admin.layouts.admin')

@section('content')
<section class="content pt-3" id="contentContainer">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card card-secondary">
                    <div class="card-header">
                        <h3 class="card-title">All Orders</h3>
                    </div>
                    <div class="card-body">
                        <table id="pending-orders-table" class="table table-bordered table-striped table-fluid">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Name/Email/Phone</th>
                                    <th>Subtotal</th>
                                    <th>Vat</th>
                                    <th>Discount</th>
                                    <th>Total</th>
                                    <th>Paid</th>
                                    <th>Due</th>
                                    <th>Payment Type</th>
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
        var userId = '{{ $userId }}';

        $('#pending-orders-table').DataTable({
            processing: true,
            serverSide: true,
            pageLength: 50,
            ajax: {
                url: "{{ route('getinhouseorder') }}",
                type: 'GET',
                data: function (d) {
                    if (userId) {
                        d.userId = userId;
                    }
                },
                error: function (xhr, error, thrown) {
                    console.error(xhr.responseText);
                }
            },
            columns: [
                { data: 'purchase_date', name: 'purchase_date' },
                { data: 'name', name: 'name' },
                { data: 'subtotal_amount', name: 'subtotal_amount' },
                { data: 'vat_amount', name: 'vat_amount' },
                { data: 'discount_amount', name: 'discount_amount' },
                { data: 'net_amount', name: 'net_amount' },
                { data: 'paid_amount', name: 'paid_amount' },
                { data: 'due_amount', name: 'due_amount' },
                { data: 'payment_method', name: 'payment_method' },
                { data: 'status', name: 'status' },
                { data: 'action', name: 'action', orderable: false, searchable: false }
            ]
        });
    });
</script>
@endsection