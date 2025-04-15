@extends('admin.layouts.admin')

@section('content')


<section class="content  pt-3">
    <div class="container-fluid">
      <div class="row">
        <div class="col-12">
          {{-- <div class="callout callout-info">
            <h5><i class="fas fa-info"></i> Note:</h5>
            This page has been enhanced for printing. Click the print button at the bottom of the invoice to test.
          </div> --}}

                  
          @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

          <!-- Main content -->
          <div class="invoice p-3 mb-3">
            <!-- title row -->
            <div class="row">
              <div class="col-12">
                <h4>
                   Customer Information
                  <small class="float-right">Date: {{ \Carbon\Carbon::parse($order->purchase_date)->format('d-m-Y') }}</small>
                </h4>
              </div>
              <!-- /.col -->
            </div>
            <!-- info row -->
            <div class="row invoice-info">
              <div class="col-sm-4 invoice-col">
                <address>
                    <strong>Name:</strong> {{ $order->user->name ?? $order->name }} {{ $order->user->surname ?? '' }}<br>
                    <strong>Email:</strong> {{ $order->user->email ?? $order->email }}<br>
                    <strong>Phone:</strong> {{ $order->user->phone ?? $order->phone }}<br>
                    <strong>Address:</strong>
                        @php
                            $addressParts = [
                                ($order->user->house_number ?? $order->house_number),
                                ($order->user->street_name ?? $order->street_name),
                                ($order->user->town ?? $order->town),
                                ($order->user->postcode ?? $order->postcode)
                            ];
                        @endphp
                        {{ implode(', ', array_filter($addressParts)) }}
                </address>
                
                
                
              </div>
              <!-- /.col -->
              <div class="col-sm-4 invoice-col">  </div>
              <!-- /.col -->
              <div class="col-sm-4 invoice-col">
                <h4 class="mb-3">Order Information</h4>
                <strong>Invoice:</strong> {{ $order->invoice }} <br>
                <strong>Purchase Date:</strong> {{ \Carbon\Carbon::parse($order->purchase_date)->format('d-m-Y') }} <br>
                {{-- <strong>Payment Method:</strong> 
                    @if($order->payment_method === 'paypal')
                        PayPal
                    @elseif($order->payment_method === 'stripe')
                        Stripe
                    @elseif($order->payment_method === 'cashOnDelivery')
                        Cash On Delivery
                    @else
                        {{ ucfirst($order->payment_method) }}
                    @endif
                    <br> --}}
                <strong>Status:</strong> 
                    @if ($order->status === 1)
                        Pending
                    @elseif ($order->status === 2)
                        Processing
                    @elseif ($order->status === 3)
                        Packed
                    @elseif ($order->status === 4)
                        Shipped
                    @elseif ($order->status === 5)
                        Delivered
                    @elseif ($order->status === 6)
                        Returned
                    @elseif ($order->status === 7)
                        Cancelled
                    @else
                        Unknown
                    @endif
                    <br>
                {{-- <strong>Order Type:</strong> {{ $order->order_type === 1 ? 'In House' : 'Frontend' }} <br> --}}
                {{-- <strong>Note:</strong> {!! $order->note !!} --}}
              </div>
              <!-- /.col -->
            </div>
            <!-- /.row -->

            <!-- Table row -->
            <div class="row mt-3">
              <div class="col-12 table-responsive">
                
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Product Image</th>
                            <th>Product Name</th>
                            <th>Quantity</th>
                            <th>Price per Unit</th>
                            <th>Total Price</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($order->orderDetails as $orderDetail)
                            <tr>
                                <td>
                                    @if($orderDetail->product)
                                        <img src="{{ asset('/images/products/' . $orderDetail->product->feature_image) }}" alt="{{ $orderDetail->product->name }}" style="width: 100px; height: auto;">
                                    @elseif($order->bundleProduct)
                                        <img src="{{ asset('/images/bundle_product/' . $order->bundleProduct->feature_image) }}" alt="{{ $order->bundleProduct->name }}" style="width: 100px; height: auto;">
                                    @else
                                        N/A
                                    @endif
                                </td>
                                <td>
                                    @if($orderDetail->product)
                                        {{ $orderDetail->product->name ?? 'N/A' }}
                                    @elseif($order->bundleProduct)
                                        {{ $order->bundleProduct->name }}
                                    @else
                                        N/A
                                    @endif
                                </td>

                                <td>{{ $orderDetail->quantity }}</td>
                                <td>{{ number_format($orderDetail->price_per_unit, 2) }}</td>
                                <td>{{ number_format($orderDetail->total_price, 2) }}</td>
                            </tr>
                            @if($orderDetail->buyOneGetOne)
                                <tr>
                                    <td colspan="8" style="background-color: #f9f9f9;">
                                        <strong style="display: block; margin-bottom: 10px;">Free Products:</strong>
                                        <div style="display: flex; flex-wrap: wrap;">
                                            @php
                                                $bogoProductIds = json_decode($orderDetail->buyOneGetOne->get_product_ids);
                                            @endphp
                                            @if(is_array($bogoProductIds))
                                                @foreach($bogoProductIds as $productId)
                                                    @if($productId)
                                                        @php
                                                            $bogoProduct = \App\Models\Product::find($productId);
                                                        @endphp
                                                        @if($bogoProduct)
                                                            <div style="display: flex; flex-direction: column; align-items: center; margin-right: 20px; margin-bottom: 10px;">
                                                                <img src="{{ asset('/images/products/' . $bogoProduct->feature_image) }}" alt="{{ $bogoProduct->name }}" style="width: 100px; height: auto; margin-bottom: 5px;">
                                                                <span>{{ $bogoProduct->name }}</span>
                                                            </div>
                                                        @endif
                                                    @endif
                                                @endforeach
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endif
                            @if($order->bundleProduct)
                                <tr>
                                    <td colspan="8" style="background-color: #f1f1f1;">
                                        <strong style="display: block; margin-bottom: 10px;">Bundle Products:</strong>
                                        <div style="display: flex; flex-wrap: wrap;">
                                            @php
                                                $bundleProductIds = json_decode($orderDetail->bundle_product_ids);
                                            @endphp
                                            @if(is_array($bundleProductIds))
                                                @foreach($bundleProductIds as $productId)
                                                    @if($productId)
                                                        @php
                                                            $bundleProduct = \App\Models\Product::find($productId);
                                                        @endphp
                                                        @if($bundleProduct)
                                                            <div style="display: flex; flex-direction: column; align-items: center; margin-right: 20px; margin-bottom: 10px;">
                                                                <img src="{{ asset('images/products/' . $bundleProduct->feature_image) }}" alt="{{ $bundleProduct->name }}" style="width: 100px; height: auto; margin-bottom: 5px;">
                                                                <span>{{ $bundleProduct->name }}</span>
                                                            </div>
                                                        @else
                                                            <div style="display: flex; flex-direction: column; align-items: center; margin-right: 20px; margin-bottom: 10px;">
                                                                <span>Product not found</span>
                                                            </div>
                                                        @endif
                                                    @endif
                                                @endforeach
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endif
                        @endforeach
                    </tbody>
                </table>
              </div>
              <!-- /.col -->
            </div>
            <!-- /.row -->

            <div class="row">
              <!-- accepted payments column -->
              <div class="col-6"></div>
              <!-- /.col -->
              <div class="col-6">
                
                <div class="table-responsive">
                  <table class="table">
                    <tr>
                      <th style="width:50%">Subtotal:</th>
                      <td>{{ number_format($order->subtotal_amount, 2) }}</td>
                    </tr>
                    <tr>
                      <th>Vat Amount</th>
                      <td> {{ $order->vat_amount }}</td>
                    </tr>
                    <tr>
                      <th>Shipping:</th>
                      <td>{{ number_format($order->shipping_amount, 2) }}</td>
                    </tr>
                    <tr>
                      <th>Discount:</th>
                      <td>{{ number_format($order->discount_amount, 2) }}</td>
                    </tr>
                    <tr>
                      <th>Total:</th>
                      <td>{{ number_format($order->net_amount, 2) }}</td>
                    </tr>
                  </table>


                </div>
              </div>
              <!-- /.col -->
            </div>
            <!-- /.row -->

            <!-- this row will not appear when printing -->
            <div class="row no-print">
              <div class="col-12">

                @if($order->order_type === 2)
                    <a href="{{ route('order-edit', ['orderId' => $order->id]) }}" class="btn btn-success float-right">
                        <i class="far fa-credit-card"></i> Create Order
                    </a>
                @endif
                
                @if ($order->order_type === 0)
                <a href="{{ route('generate-pdf', ['encoded_order_id' => base64_encode($order->id)]) }}" class="btn btn-success float-right" target="_blank"  style="margin-right: 5px;">
                    <i class="fas fa-receipt"></i> Download
                </a>
                @else
                <a href="{{ route('in-house-sell.generate-pdf', ['encoded_order_id' => base64_encode($order->id)]) }}" class="btn btn-success float-right" target="_blank"  style="margin-right: 5px;">
                    <i class="fas fa-receipt"></i> Download
                </a>
                @endif


              </div>
            </div>
          </div>
          <!-- /.invoice -->
        </div><!-- /.col -->
      </div><!-- /.row -->
    </div><!-- /.container-fluid -->
  </section>




<section class="content pt-3 d-none" id="contentContainer">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card card-secondary">
                    <div class="card-header">
                        <h3 class="card-title">Order Details</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <!-- User Information -->
                            <div class="col-md-6">
                            </div>
                            <!-- Order Information -->
                            <div class="col-md-6">
                            </div>
                        </div>

                        <!-- Product Details -->
                        <div class="row mt-4">
                            <div class="col-12">
                                <h4 class="mb-3">Product Details</h4>
                                <table class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>Product Image</th>
                                            <th>Product Name</th>
                                            <th>Quantity</th>
                                            <th>Size</th>
                                            <th>Color</th>
                                            <th>Price per Unit</th>
                                            <th>Total Price</th>
                                            <th>Supplier</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($order->orderDetails as $orderDetail)
                                            <tr>
                                                <td>
                                                    @if($orderDetail->product)
                                                        <img src="{{ asset('/images/products/' . $orderDetail->product->feature_image) }}" alt="{{ $orderDetail->product->name }}" style="width: 100px; height: auto;">
                                                    @elseif($order->bundleProduct)
                                                        <img src="{{ asset('/images/bundle_product/' . $order->bundleProduct->feature_image) }}" alt="{{ $order->bundleProduct->name }}" style="width: 100px; height: auto;">
                                                    @else
                                                        N/A
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($orderDetail->product)
                                                        {{ $orderDetail->product->name ?? 'N/A' }}
                                                    @elseif($order->bundleProduct)
                                                        {{ $order->bundleProduct->name }}
                                                    @else
                                                        N/A
                                                    @endif
                                                </td>

                                                <td>{{ $orderDetail->quantity }}</td>
                                                <td>{{ $orderDetail->size }}</td>
                                                <td>{{ $orderDetail->color }}</td>
                                                <td>{{ number_format($orderDetail->price_per_unit, 2) }}</td>
                                                <td>{{ number_format($orderDetail->total_price, 2) }}</td>
                                                <td>
                                                    @if($orderDetail->supplier)
                                                        {{ $orderDetail->supplier->name }}
                                                    @else
                                                        N/A
                                                    @endif
                                                </td>
                                            </tr>
                                            @if($orderDetail->buyOneGetOne)
                                                <tr>
                                                    <td colspan="8" style="background-color: #f9f9f9;">
                                                        <strong style="display: block; margin-bottom: 10px;">Free Products:</strong>
                                                        <div style="display: flex; flex-wrap: wrap;">
                                                            @php
                                                                $bogoProductIds = json_decode($orderDetail->buyOneGetOne->get_product_ids);
                                                            @endphp
                                                            @if(is_array($bogoProductIds))
                                                                @foreach($bogoProductIds as $productId)
                                                                    @if($productId)
                                                                        @php
                                                                            $bogoProduct = \App\Models\Product::find($productId);
                                                                        @endphp
                                                                        @if($bogoProduct)
                                                                            <div style="display: flex; flex-direction: column; align-items: center; margin-right: 20px; margin-bottom: 10px;">
                                                                                <img src="{{ asset('/images/products/' . $bogoProduct->feature_image) }}" alt="{{ $bogoProduct->name }}" style="width: 100px; height: auto; margin-bottom: 5px;">
                                                                                <span>{{ $bogoProduct->name }}</span>
                                                                            </div>
                                                                        @endif
                                                                    @endif
                                                                @endforeach
                                                            @endif
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endif
                                            @if($order->bundleProduct)
                                                <tr>
                                                    <td colspan="8" style="background-color: #f1f1f1;">
                                                        <strong style="display: block; margin-bottom: 10px;">Bundle Products:</strong>
                                                        <div style="display: flex; flex-wrap: wrap;">
                                                            @php
                                                                $bundleProductIds = json_decode($orderDetail->bundle_product_ids);
                                                            @endphp
                                                            @if(is_array($bundleProductIds))
                                                                @foreach($bundleProductIds as $productId)
                                                                    @if($productId)
                                                                        @php
                                                                            $bundleProduct = \App\Models\Product::find($productId);
                                                                        @endphp
                                                                        @if($bundleProduct)
                                                                            <div style="display: flex; flex-direction: column; align-items: center; margin-right: 20px; margin-bottom: 10px;">
                                                                                <img src="{{ asset('images/products/' . $bundleProduct->feature_image) }}" alt="{{ $bundleProduct->name }}" style="width: 100px; height: auto; margin-bottom: 5px;">
                                                                                <span>{{ $bundleProduct->name }}</span>
                                                                            </div>
                                                                        @else
                                                                            <div style="display: flex; flex-direction: column; align-items: center; margin-right: 20px; margin-bottom: 10px;">
                                                                                <span>Product not found</span>
                                                                            </div>
                                                                        @endif
                                                                    @endif
                                                                @endforeach
                                                            @endif
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endif
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="row mt-4">
                            <div class="col-12">
                                <a href="{{ url()->previous() }}" class="btn btn-secondary">Back</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
