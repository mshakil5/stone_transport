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
                                    <th>Consignment Number</th>
                                    <th>Mother Vessel</th>
                                    <th>Payment Type</th>
                                    <th>Payment Amount</th>
                                    <th>Quantity</th>
                                    <th>Purchase</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($data as $key => $order)
                                <tr>
                                    <td>{{ $key + 1 }}</td>
                                    <td>{{ $order->consignment_number }}</td>
                                    <td>{{ $order->motherVessel->name }}</td>
                                    <td>{{ $order->payment_type }}</td>
                                    <td>{{ $order->payment_amount }}</td>
                                    <td>{{ $order->quantity }}</td>
                                    <td>
                                        <a href="{{ route('purchase.edit', $order->id) }}" class="btn btn-sm btn-info">
                                            Add To Stock
                                        </a>
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

@endsection

@section('script')

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
