@extends('admin.layouts.admin')

@section('content')

<section class="content pt-3" id="contentContainer">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card card-secondary">
                    <div class="card-header">
                        <h3 class="card-title">Stock Return History</h3>
                    </div>
                    <div class="card-body">
                        <table id="stockReturnTable" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Product</th>
                                    <th>Return Quantity</th>
                                    <th>Reason</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($purchaseReturns as $purchaseReturn)
                                <tr>
                                   <td>{{ \Illuminate\Support\Carbon::parse($purchaseReturn->date)->format('d-m-Y') }}</td>
                                    <td>{{ $purchaseReturn->product->name }}</td>
                                    <td>{{ $purchaseReturn->return_quantity }}</td>
                                    <td>{!! $purchaseReturn->reason !!}</td>
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

@endsection

@section('script')

<script>
    $(document).ready(function() {
        $('#stockReturnTable').DataTable({
            "responsive": true,
            "lengthChange": false,
            "autoWidth": false,
            "buttons": ["copy", "csv", "excel", "pdf", "print"]
        }).buttons().container().appendTo('#stockReturnTable_wrapper .col-md-6:eq(0)');
    });
</script>

@endsection
