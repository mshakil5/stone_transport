@extends('admin.layouts.admin')

@section('content')

@if(session('session_clear'))
  <script>
      localStorage.removeItem('wishlist');
      localStorage.removeItem('cart');
      @php
          session()->forget('session_clear');
      @endphp
  </script>
@endif

<!-- Content Header (Page header) -->
<div class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1 class="m-0">Dashboard</h1>
      </div><!-- /.col -->
    </div><!-- /.row -->
  </div><!-- /.container-fluid -->
</div>
<!-- /.content-header -->

<!-- content area -->
<section class="content">
  <div class="container-fluid">
    <!-- Small boxes (Stat box) -->
    <div class="row">
      <div class="col-lg-3 col-6">
        <!-- small box -->
        <div class="small-box bg-warning">
          <div class="inner">

            @php
            $userCount = \App\Models\User::where('is_type', 0)->count();
            @endphp

            <h3>{{ $userCount }}</h3>

            <p>Customers Count</p>
          </div>
          <div class="icon">
            <i class="ion ion-person-add"></i>
          </div>
          <a href="{{ route('allcustomer') }}" class="small-box-footer">All Customers <i class="fas fa-arrow-circle-right"></i></a>
        </div>
      </div>
      <!-- ./col -->
      <div class="col-lg-3 col-6">
        <div class="small-box bg-info">
          <div class="inner">
            @php
            use Carbon\Carbon;
            $today = Carbon::today()->toDateString();
            $ordersCount = \App\Models\Order::select('id')
            ->whereDate('created_at', $today)
            ->count();
            @endphp
            <h3>{{$ordersCount}}</h3>
            <p>Today's Orders</p>
          </div>
          <div class="icon">
            <i class="ion ion-bag"></i>
          </div>
          <a href="{{ route('getallorder') }}" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
        </div>
      </div>
      <!-- ./col -->
      <div class="col-lg-3 col-6">
        <!-- small box -->
        <div class="small-box bg-success">
          <div class="inner">
            @php
              $totalQty = \App\Models\Stock::sum('quantity');
            @endphp
            
            <h3>{{ number_format($totalQty, 0) }} <sup style='font-size: 20px'></sup></h3>
            <p>Total Stock Product</p>
          </div>
          <div class="icon">
            <i class="ion ion-stats-bars"></i>
          </div>
          <a class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
        </div>
      </div>
      <!-- ./col -->
      <div class="col-lg-3 col-6">
        <!-- small box -->
        <div class="small-box bg-danger">
          <div class="inner">

            @php
            $currentMonth = Carbon::now()->month;
            $currentYear = Carbon::now()->year;
            $ordersCount = \App\Models\Order::whereMonth('created_at', $currentMonth)
            ->whereYear('created_at', $currentYear)
            ->count();
            @endphp
            <h3>{{ $ordersCount }}</h3>

            <p>This Month's Orders</p>
          </div>
          <div class="icon">
            <i class="ion ion-clipboard"></i>
          </div>
          <a href="{{ route('getallorder') }}" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
        </div>
      </div>
      <!-- ./col -->
    </div>

    <div class="row">
      <section class="col-lg-5 connectedSortable">
        <div class="card">
          <div class="card-header">
            <h3 class="card-title">
              <i class="ion ion-clipboard mr-1"></i>
              New Orders
            </h3>
          </div>
          <div class="card-body">
            <ul class="todo-list" data-widget="todo-list">
              @php
              use App\Models\Order;

              $newOrders = Order::where('admin_notify', 1)
              ->select('id', 'created_at', 'user_id', 'net_amount', 'name', 'surname')
              ->orderBy('id', 'desc')
              ->get();
              @endphp

              @forelse($newOrders as $order)
              <li id="order-{{ $order->id }}">
                @php
                $user = $order->name . ' ' . $order->surname;
                $createdAt = Carbon::parse($order->created_at);
                $timeDiff = $createdAt->diffForHumans();
                $timeDiffInHours = $createdAt->diffInHours();
                $badgeClass = '';

                if ($timeDiffInHours <= 1) {
                  $badgeClass='badge-primary' ;
                  } elseif ($timeDiffInHours <=6) {
                  $badgeClass='badge-secondary' ;
                  } elseif ($timeDiffInHours <=24) {
                  $badgeClass='badge-info' ;
                  } elseif ($timeDiffInHours <=168) {
                  $badgeClass='badge-warning' ;
                  } else {
                  $badgeClass='badge-danger' ;
                  }
                  @endphp
                  <div class="icheck-primary d-inline ml-2">
                  <input type="checkbox" value="" name="todo{{ $order->id }}" id="todoCheck{{ $order->id }}" onclick="markAsNotified({{ $order->id }})">
                  <label for="todoCheck{{ $order->id }}">{{ $user }}</label>
          </div>
          <span class="text">
            <span style="color: #007bff; font-weight: bold;"></span> has ordered a new item.
            <span style="color: #28a745; font-weight: bold;">Net Amount: ${{ number_format($order->net_amount, 2) }}</span>
          </span>

          <small class="badge {{ $badgeClass }}"><i class="far fa-clock"></i> {{ $timeDiff }}</small>
          <a href="{{ route('admin.orders.details', ['orderId' => $order->id]) }}" class="btn btn-sm btn-info float-right">View Order</a>
          </li>
          @empty
          <li>No new orders</li>
          @endforelse
          </ul>
        </div>
    </div>
</section>

<section class="col-lg-7">
  <div class="card">
    <div class="card-header">
      <h3 class="card-title">
        <i class="ion ion-clipboard mr-1"></i>
        Product Quantity in warehouse
      </h3>
      <div class="card-tools">
        <a href="{{route('productHistory')}}" class="btn btn-tool">
            <i class="fas fa-envelope"></i>
        </a>
      </div>
    </div>
    <div class="card-body">
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
    </section>

</div>

</div>
</section>

@endsection

@section('script')

<script>
  function markAsNotified(orderId) {
    $.ajax({
      url: '{{ route("orders.notify") }}',
      method: 'POST',
      data: {
        _token: '{{ csrf_token() }}',
        order_id: orderId
      },
      success: function(response) {
        if (response.success) {
          $('#order-' + orderId).remove();
        }
      },
      error: function(xhr) {
        console.error('Error marking order as notified:', xhr);
      }
    });
  }
</script>

@endsection