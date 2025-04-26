@extends('admin.layouts.admin')

@section('content')

@php
    $systemLosses = \App\Models\SystemLose::with('product')->where('product_id', $product->id)->where('warehouse_id', $warehouse_id)->latest()->get();
@endphp

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

        
        <div class="row d-none">
            <div class="col-12">
                <div class="card card-secondary">
                    <div class="card-header">
                        <h3 class="card-title">Search</h3>
                    </div>
                    <div class="card-body">
                        <!-- Filter Form Section -->
                        <form method="POST">
                            @csrf
                            <div class="row mb-3 ">
                                <input type="hidden" id="product_id" name="product_id" value="{{$id}}">
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
                    <div class="card-body">

                        <div class="text-center mb-4 company-name-container">
                            <h2>{{$product->name}}</h2>
                            <h4>Purchase history</h4>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-bordered table-striped p-table">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Supplier</th>
                                        <th>Warehouse</th>
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
                                            <td>{{ date('d-m-Y', strtotime($data->created_at))}}</td>
                                            <td>
                                                @if ($data->purchase && $data->purchase->supplier)
                                                {{ $data->purchase->supplier->name}}
                                                <a href="{{route('supplier.purchase', $data->purchase->supplier->id)}}" class="btn btn-sm btn-success" target="blank">
                                                    <i class="fas fa-arrow-right"></i>
                                                </a>
                                                @endif
                                            </td>
                                            <td>{{ $data->warehouse->name}}</td>
                                            <td>{{ $data->quantity}}</td>
                                            <td>{{ number_format($data->purchase_price, 2) }}</td>
                                            <td>{{ number_format($data->total_vat, 2) }}</td>
                                            <td>{{ number_format($data->total_amount_with_vat, 2) }}</td>
                                            <td>{{ number_format($purchaseHistories->sum('total_amount_with_vat'), 2) }}</td>
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
                    <div class="card-body">

                        <div class="text-center mb-4 company-name-container">
                            <h2>{{$product->name}}</h2>
                            <h4>Sales history</h4>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-bordered table-striped p-table">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Whole Saler</th>
                                        <th>Warehouse</th>
                                        <th>Quantity</th>
                                        <th>Price</th>
                                        <th>Status</th>
                                        <th>Total Price</th>
                                        <th>Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($salesHistories as $key => $data)
                                        <tr>
                                            <td>{{ date('d-m-Y', strtotime($data->created_at))}}</td>
                                            <td>{{ $data->order->user->name ?? ''}} 
                                                <a href="{{route('getallorder', $data->order->user->id )}}" class="btn btn-sm btn-success">
                                                    <i class="fas fa-arrow-right"></i>
                                                </a>
                                            </td>
                                            <td>{{ $data->warehouse_id ? $data->warehouse->name : " "}}</td>      
                                            <td>{{ $data->quantity}}</td>
                                            <td>{{ number_format($data->price_per_unit, 2) }}</td>
                                            <td>
                                              @if ($data->order->status == 1)
                                                  <span class="btn btn-sm btn-primary">Pending</span>
                                              @elseif ($data->order->status == 2)
                                                 <span class="btn btn-sm btn-info">Processing</span> 
                                              @elseif ($data->order->status == 3)
                                                  <span class="btn btn-sm btn-primary">Packed</span>
                                              @elseif ($data->order->status == 4)
                                                  <span class="btn btn-sm btn-info">Shipped</span>
                                              @elseif ($data->order->status == 5)                                            
                                                  <span class="btn btn-sm btn-success">Delivered</span>
                                              @elseif ($data->order->status == 6)                                   
                                                  <span class="btn btn-sm btn-warning">Returned</span>
                                              @elseif ($data->order->status == 7)
                                                  <span class="btn btn-sm btn-danger">Cancelled</span>
                                              @else
                                                  
                                              @endif</td>
                                              <td>{{ number_format($data->total_price, 2) }}</td>
                                              <td>{{ number_format($salesHistories->sum('total_price'), 2) }}</td>              
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
                  <div class="card-body">

                      <div class="text-center mb-4 company-name-container">
                          <h2>{{ $product->name }}</h2>
                          <h4>System Loss</h4>
                      </div>

                      <div class="table-responsive">
                          <table class="table table-bordered table-striped p-table">
                              <thead>
                                  <tr>
                                      <th>Date</th>
                                      <th>Quantity</th>
                                      <th>Warehouse</th>
                                      <th>Performed By</th>
                                      <th>Reason</th>
                                  </tr>
                              </thead>
                              <tbody>
                                  @foreach ($systemLosses as $key => $systemLoss)
                                  <tr>
                                      <td>{{ \Carbon\Carbon::parse($systemLoss->created_at)->format('d-m-Y') }}</td>
                                      <td>{{ $systemLoss->quantity }}</td>
                                      <td>
                                          @if ($systemLoss->shipment_detail_id)
                                              <span class="text-danger">Before Stocking</span>
                                          @else
                                              {{ $systemLoss->warehouse->name ?? '' }}
                                          @endif
                                      </td>
                                      <td>{{ $systemLoss->user->name ?? '' }}</td>
                                      <td>{!! $systemLoss->reason !!}</td>
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
        $('.p-table').DataTable();
    });
</script>

@endsection