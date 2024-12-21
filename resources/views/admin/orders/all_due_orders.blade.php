@extends('admin.layouts.admin')

@section('content')
<section class="content pt-3" id="contentContainer">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">

                <div class="mb-3">
                    <a href="{{ route('allcustomer') }}" class="btn btn-secondary">
                    <i class="fa fa-arrow-left"></i> Back
                    </a>
                </div>

                <div class="card card-secondary">
                    <div class="card-header">
                        <h3 class="card-title">All Due Orders</h3>
                    </div>
                    <div class="card-body">
                        <table id="example1" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Name/Email/Phone</th>
                                    <th>Total Amount</th> 
                                    <th>Due Status</th> 
                                    <th>Due Amount</th> 
                                    <th>Received Amount</th> 
                                    <th>Time</th>     
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($orders as $order)
                                <tr>
                                    <td>{{ \Carbon\Carbon::parse($order->purchase_date)->format('d-m-Y') }}</td>
                                    <td>
                                        {{ optional($order->user)->name ?? $order->name }} {{ optional($order->user)->surname ?? '' }} <br> {{ optional($order->user)->email ?? $order->email }} <br> {{ optional($order->user)->phone ?? $order->phone }}
                                    </td>
                                    <td><span class="btn btn-sm btn-warning">£ {{ number_format($order->net_amount, 2) }} </span></td>
                                    <td>
                                        @if ($order->due_status == 1)
                                            <span class="badge bg-success">Full Received</span>
                                        @else
                                            <span class="badge bg-danger">Due Pending</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="align-items-center">

                                        @php
                                            $netAmount = $order->due_amount - $order->received_amount;
                                        @endphp

                                        @if ($netAmount > 0)
                                            <span class="btn btn-sm btn-danger">£ {{ number_format($netAmount, 2) }}</span>
                                        @endif
                                        </div>
                                    </td>
                                    <td>{{ number_format($order->received_amount, 2) }}</td>
                                    <td>
                                        <span class="btn btn-sm btn-info">{{ \Carbon\Carbon::parse($order->created_at)->diffForHumans() }} </span>
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
                <h5 class="modal-title" id="payModalLabel">WholeSaler Payment Form</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="payForm">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="paymentAmount">Payment Amount</label>
                        <input type="number" class="form-control" id="paymentAmount" name="paymentAmount" placeholder="Enter payment amount">
                    </div>
                    
                    <div class="form-group">
                        <label for="document">Document</label>
                        <input type="file" class="form-control-file" id="document" name="document">
                    </div>

                    <div class="form-group">
                        <label for="payment_type">Payment Type</label>
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
                    <button type="submit" class="btn btn-warning">Receive</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('script')

<script>
    $(function () {
        $("#example1").DataTable({
            "responsive": true,
            "lengthChange": false,
            "autoWidth": false,
            "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"]
        }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
    });
</script>

@endsection