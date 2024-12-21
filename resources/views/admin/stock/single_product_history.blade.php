@extends('admin.layouts.admin')

@section('content')
<section class="content" id="newBtnSection">
    <div class="container-fluid">
      <div class="row">
        <div class="col-2">
            <a href="{{route('allstock')}}" class="btn btn-secondary my-3">Back</a>
        </div>
      </div>
    </div>
</section>
<section class="content pt-3" id="contentContainer">
    <div class="container-fluid">

        
        <div class="row">
            <div class="col-12">
                <div class="card card-secondary">
                    <div class="card-header">
                        <h3 class="card-title">Search</h3>
                    </div>
                    <div class="card-body">
                        <!-- Filter Form Section -->
                        <form action="{{route('admin.product.purchasehistorysearch',['id' => $id, 'size' => $size, 'color' => $color])}}" method="POST">
                            @csrf
                            <div class="row mb-3 ">
                                <input type="hidden" id="product_id" name="product_id" value="{{$id}}">
                                <input type="hidden" id="size" name="size" value="{{$size}}">
                                <input type="hidden" id="color" name="color" value="{{$color}}">
                                <div class="col-md-3 d-none">
                                    <label class="label label-primary">Filter By</label>
                                    <select class="form-control" id="filterBy" name="filterBy">
                                        <option value="today">Today</option>
                                        <option value="this_week">This Week</option>
                                        <option value="this_month">This Month</option>
                                        <option value="start_of_month">Start of the Month</option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label class="label label-primary">From Date</label>
                                    <input type="date" class="form-control" id="fromDate" name="fromDate">
                                </div>
                                <div class="col-md-2">
                                    <label class="label label-primary">To Date</label>
                                    <input type="date" class="form-control" id="toDate" name="toDate" value="{{old('toDate')}}">
                                </div>
                                <div class="col-md-3">
                                    <label class="label label-primary">Warehouses</label>
                                    <select class="form-control select2" id="warehouse_id" name="warehouse_id">
                                        <option value="">Select...</option>
                                        @foreach($warehouses as $warehouse)
                                            <option value="{{ $warehouse->id }}">{{ $warehouse->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label class="label label-primary" style="visibility:hidden;">Action</label>
                                    <button type="submit" class="btn btn-secondary btn-block">Search</button>
                                </div>
                                <div class="col-md-12">
                                    @if ($errors->any())
                                            <ul>
                                                @foreach ($errors->all() as $error)
                                                    <li class="text-danger">{{ $error }}</li>
                                                @endforeach
                                            </ul>
                                    @endif
                                </div>
                            </div>
                        </form>
                        <!-- End of Filter Form Section -->
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card card-secondary">
                    <div class="card-header">
                        <h3 class="card-title">Product Name: {{$product->name}}-{{$product->product_code}}</h3>
                    </div>
                    <div class="card-body">

                        <div class="text-center mb-4 company-name-container">
                            <h2>{{$product->name}}</h2>
                            <h4>Purchase history</h4>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-bordered table-striped" id="p-table">
                                <thead>
                                    <tr>
                                        <th>Sl</th>
                                        <th>Date</th>
                                        <th>Supplier</th>
                                        <th>Quantity</th>
                                        <th>Price</th>
                                        <th>Vat Amount</th>
                                        <th>Total Price</th>
                                        <th>Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($purchaseHistories as $key => $data)
                                        <tr>
                                            <td>{{ $key + 1}}</td>
                                            <td>{{ date('d-m-Y', strtotime($data->created_at))}}</td>
                                            <td>
                                                @if ($data->purchase && $data->purchase->supplier)
                                                {{ $data->purchase->supplier->name}}
                                                <a href="{{route('supplier.purchase', $data->purchase->supplier->id)}}" class="btn btn-sm btn-success" target="blank">
                                                    <i class="fas fa-arrow-right"></i>
                                                </a>
                                                @endif
                                            </td>
                                            <td>{{ $data->quantity}}</td>
                                            <td>{{ $data->purchase_price}}</td>
                                            <td>{{ $data->total_vat}}</td>
                                            <td>{{ $data->total_amount_with_vat}}</td>
                                            <td>{{ $purchaseHistories->sum('total_amount_with_vat')}}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        
        <div class="row">
            <div class="col-12">
                <div class="card card-secondary">
                    <div class="card-header">
                        <h3 class="card-title">Product Name: {{$product->name}}-{{$product->product_code}}</h3>
                    </div>
                    <div class="card-body">

                        <div class="text-center mb-4 company-name-container">
                            <h2>{{$product->name}}</h2>
                            <h4>Sales history</h4>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-bordered table-striped" id="p-table">
                                <thead>
                                    <tr>
                                        <th>Sl</th>
                                        <th>Date</th>
                                        <th>Whole Saler</th>
                                        <th>Warehouse</th>
                                        <th>Quantity</th>
                                        <th>Price</th>
                                        <th>Vat Amount</th>
                                        <th>Total Price</th>
                                        <th>Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($salesHistories as $key => $data)
                                        <tr>
                                            <td>{{ $key + 1}}</td>
                                            <td>{{ date('d-m-Y', strtotime($data->created_at))}}</td>
                                            <td>{{ $data->order->user->name}} 
                                                <a href="{{route('getallorder', $data->order->user->id )}}" class="btn btn-sm btn-success">
                                                    <i class="fas fa-arrow-right"></i>
                                                </a>
                                            </td>
                                            <td>{{ $data->warehouse_id ? $data->warehouse->name : " "}}</td>
                                            <td>{{ $data->quantity}}</td>
                                            <td>{{ $data->price_per_unit}}</td>
                                            <td></td>
                                            <td>{{ $data->total_price}}</td>
                                            <td>{{ $salesHistories->sum('total_price')}}</td>
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
        

        $('#p-table').DataTable();




        // $('.select2').select2({
        //     placeholder: 'Select a warehouse',
        //     allowClear: true
        // });
        // $('.select2').css('width', '100%');
    });
</script>

@endsection