@extends('admin.layouts.admin')

@section('content')

<section class="content pt-3" id="contentContainer">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card card-secondary">
                    <div class="card-header">
                        <h3 class="card-title">Product Information</h3>

                        <a href="#" class="text-end">  </a>

                        <div class="card-tools">
                            <a href="{{route('productHistory')}}" class="btn btn-tool">
                                <i class="fas fa-envelope"></i>
                            </a>
                        </div>

                    </div>
                    <div class="card-body">

                    <!-- Filter Form Section -->
                    <form action="#" method="GET" class="">
                        <div class="row mb-3">
                            
                            <div class="col-md-2">
                                <label class="label label-primary">Product</label>
                                <select class="form-control select2" id="product_id" name="product_id">
                                    <option value="">Select...</option>
                                    @foreach($products as $product)
                                        <option value="{{ $product->id }}">{{ $product->name }}-{{ $product->product_code }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-2">
                                <label class="label label-primary">Warehouses</label>
                                <select class="form-control select2" id="warehouse_id" name="warehouse_id">
                                    <option value="">Select...</option>
                                    @foreach($warehouses as $warehouse)
                                        <option value="{{ $warehouse->id }}">{{ $warehouse->name }}-{{ $warehouse->location }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="label label-primary">Supplier</label>
                                <select class="form-control select2" id="supplier_id" name="supplier_id">
                                    <option value="">Select...</option>
                                    @foreach(\App\Models\Supplier::where('status', 1)->get() as $supplier)
                                        <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
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
                                    <th>Total Product Item</th>
                                    <th>Total Stock</th>
                                    <th>Total Supplier</th>
                                    <th>Total Whole Saler</th>
                                </tr>
                            </thead>
                            <tbody>

                                <tr>
                                    <td>{{ $totalProduct }}</td>
                                    <td>{{ number_format($totalQty, 0) }}</td>
                                    <td>{{ \App\Models\Supplier::count() }}</td>
                                    <td>{{ \App\Models\User::where('is_type', 0)->count() }}</td>
                                </tr>
                                
                            </tbody>
                        </table>
                    </div>
                    <hr>
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped" id="stock-table">
                            <thead>
                                <tr>
                                    <th>Warehouse Name</th>
                                    <th>Total Stock</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $warehousestock = \App\Models\Stock::groupBy('warehouse_id')
                                            ->selectRaw('*, sum(quantity) as totalquantity')
                                            ->get();
                                @endphp     
                                @foreach ($warehousestock as $item)
                                <tr>
                                    <td>{{ $item->warehouse->name }}</td>
                                    <td>{{ number_format($item->totalquantity, 0)  }}</td>
                                </tr>
                                @endforeach
                                
                                
                            </tbody>
                        </table>
                    </div>
                    
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>



@endsection

@section('script')
<script>
    $(document).ready(function () {
        

        $('#reset-button').on('click', function() {
            location.reload();
        });

        $('#stock-table').on('click', '.btn-open-loss-modal', function () {
            let productId = $(this).data('id');
            let size = $(this).data('size');
            let color = $(this).data('color');
            openLossModal(productId, size, color);
        });

        

        $('#product_id').select2({
            placeholder: "Select product...",
            allowClear: true,
            width: '100%'
        });
    });
</script>

@endsection