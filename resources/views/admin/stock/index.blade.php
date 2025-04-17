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

                    <!-- Filter Form Section -->
                    <form action="#" method="GET">
                        <div class="row mb-3">
                            
                            <div class="col-md-3">
                                <label class="label label-primary">Product</label>
                                <select class="form-control select2" id="product_id" name="product_id">
                                    <option value="">Select...</option>
                                    @foreach($products as $product)
                                        <option value="{{ $product->id }}">{{ $product->name }}-{{ $product->product_code }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-3">
                                <label class="label label-primary">Warehouses</label>
                                <select class="form-control select2" id="warehouse_id" name="warehouse_id">
                                    <option value="">Select...</option>
                                    @foreach($warehouses as $warehouse)
                                        <option value="{{ $warehouse->id }}">{{ $warehouse->name }}-{{ $warehouse->location }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="label label-primary" style="visibility:hidden;">Action</label>
                                <button type="submit" class="btn btn-secondary btn-block">Search</button>
                            </div>
                            <div class="col-md-1">
                                <label class="label label-primary" style="visibility:hidden;">Action</label>
                                <button type="button" id="reset-button" class="btn btn-secondary btn-block">
                                    <i class="fas fa-sync-alt"></i>
                                </button>
                            </div>
                        </div>
                    </form>
                    <!-- End of Filter Form Section -->

                        <div class="table-responsive">
                            <table class="table table-bordered table-striped" id="stock-table">
                                <thead>
                                    <tr>
                                        <th>Sl</th>
                                        <th>Product Name</th>
                                        <th>Product Code</th>
                                        <th>Warehouse</th>
                                        <th>Available Qty</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- System Loss Modal -->
<div class="modal fade" id="systemLossModal" tabindex="-1" aria-labelledby="systemLossModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="systemLossModalLabel">System Loss</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="systemLossForm">
                <div class="modal-body">
                    <input type="hidden" id="lossProductId" name="productId">
                    
                    <span id="allError" class="text-danger"></span>

                    <div class="form-group">
                        <label class="label label-primary">Warehouses</label>
                        <select class="form-control" id="warehouse" name="warehouse">
                            <option value="">Select...</option>
                            @foreach($warehouses as $warehouse)
                                <option value="{{ $warehouse->id }}">{{ $warehouse->name }}-{{ $warehouse->location }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="lossQuantity">Loss Quantity:</label>
                        <input type="number" class="form-control" id="lossQuantity" name="lossQuantity" required>
                        <span id="quantityError" class="text-danger"></span>
                    </div>
                    <div class="form-group">
                        <label for="lossReason">Loss Reason:</label>
                        <textarea class="form-control" id="lossReason" name="lossReason" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Submit</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('script')
<script>
    $(document).ready(function () {
        function openLossModal(productId, size, color, warehouse) {
            // console.log(productId, size, color);

            $('#systemLossForm')[0].reset();
            $('#lossProductId').val(productId);
            $('#warehouse').val(warehouse).prop('disabled', true);
            $('#systemLossModal').modal('show');

            $('#systemLossForm').submit(function (e) {
                e.preventDefault();
                let lossQuantity = parseInt($('#lossQuantity').val());

                // if (lossQuantity > currentQuantity) {
                //     $('#quantityError').text('Quantity cannot be more than current stock quantity.');
                //     return;
                // } else {
                //     $('#quantityError').text('');
                // }

                let lossReason = $('#lossReason').val();
                let warehouse = $('#warehouse').val();

                $.ajax({
                    url: "{{ route('process.system.loss') }}", 
                    type: 'POST',
                    data: {
                        color: color,
                        size: size,
                        productId: productId,
                        warehouse: warehouse,
                        lossQuantity: lossQuantity,
                        lossReason: lossReason,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function (response) {
                        swal({
                            text: "Sent to system loss",
                            icon: "success",
                            button: {
                                text: "OK",
                                className: "swal-button--confirm"
                            }
                        });
                        $('#systemLossModal').modal('hide');
                        $('#stock-table').DataTable().ajax.reload();
                    },
                    error: function (xhr, status, error) {
                        console.error(xhr.responseText);
                        
                        let response = JSON.parse(xhr.responseText);
        
                        // If there are validation errors, get the message
                        let errorMessage = response.message;
                        // Insert the error message into the modal
                        $('#allError').html(errorMessage);
                    }
                });
            });
        }

        var table = $('#stock-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('allstocks') }}",
                data: function(d) {
                    d.warehouse_id = $('#warehouse_id').val();
                    d.product_id = $('#product_id').val();
                },
                error: function(xhr, error, code) {
                    console.error(xhr.responseText);
                }
            },
            pageLength: 50,
            columns: [
                { data: 'sl', name: 'sl', orderable: false, searchable: false },
                { data: 'product_name', name: 'product_name' },
                { data: 'product_code', name: 'product_code' },
                { data: 'warehouse', name: 'warehouse' },
                { data: 'quantity_formatted', name: 'quantity' },
                {data: 'action', name: 'action', orderable: false, searchable: false}
            ],
            // columnDefs: [
            //     {
            //         targets: [6],
            //         visible: false,
            //         searchable: false
            //     }
            // ],
            buttons: [
                'copy', 'csv', 'excel', 'pdf', 'print'
            ]
        });

        $('form').on('submit', function(e) {
            e.preventDefault();
            table.draw();
        });

        $('#reset-button').on('click', function() {
            $('#warehouse_id').val('');
            location.reload();
            table.draw();
        });

        $('#stock-table').on('click', '.btn-open-loss-modal', function () {
            let productId = $(this).data('id');
            let size = $(this).data('size');
            let color = $(this).data('color');
            let warehouse = $(this).data('warehouse');
            openLossModal(productId, size, color, warehouse);
        });

        $('#systemLossModal').on('hidden.bs.modal', function () {
            $('#systemLossForm')[0].reset();
            $('#quantityError').text('');
        });

        $('#product_id').select2({
            placeholder: "Select product...",
            allowClear: true,
            width: '100%'
        });
    });
</script>

@endsection