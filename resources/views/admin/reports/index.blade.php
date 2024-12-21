@extends('admin.layouts.admin')

@section('content')

<!-- Content Header (Page header) -->
<div class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1 class="m-0">Reports</h1>
      </div><!-- /.col -->
    </div><!-- /.row -->
  </div><!-- /.container-fluid -->
</div>
<!-- /.content-header -->

<section class="content">
    <div class="container-fluid">
        <!-- Row for Sales -->
        <div class="row">
            <!-- Daily Sales -->
            <div class="col-lg-3 col-md-6 mb-4">
                <a href="{{ route('reports.dailySale') }}" class="small-box bg-primary d-block text-center p-3 text-white rounded">
                    <div class="inner">
                        <h4 class="mb-0" style="font-size: 24px;">Daily Sales</h4>
                    </div>
                    <div class="icon">
                        <i class="ion ion-ios-analytics-outline" style="font-size: 36px;"></i>
                    </div>
                </a>
            </div>
            
            <!-- Weekly Sales -->
            <div class="col-lg-3 col-md-6 mb-4">
                <a href="{{ route('reports.weeklySale') }}" class="small-box bg-success d-block text-center p-3 text-white rounded">
                    <div class="inner">
                        <h4 class="mb-0" style="font-size: 24px;">Weekly Sales</h4>
                    </div>
                    <div class="icon">
                        <i class="ion ion-arrow-graph-up-right" style="font-size: 36px;"></i>
                    </div>
                </a>
            </div>
            
            <!-- Monthly Sales -->
            <div class="col-lg-3 col-md-6 mb-4">
                <a href="{{ route('reports.monthlySale') }}" class="small-box bg-info d-block text-center p-3 text-white rounded">
                    <div class="inner">
                        <h4 class="mb-0" style="font-size: 24px;">Monthly Sales</h4>
                    </div>
                    <div class="icon">
                        <i class="ion ion-android-calendar" style="font-size: 36px;"></i>
                    </div>
                </a>
            </div>
            
            <!-- Date-to-Date Sales -->
            <div class="col-lg-3 col-md-6 mb-4">
                <a href="{{ route('reports.dateToDateSale') }}" class="small-box bg-warning d-block text-center p-3 text-white rounded">
                    <div class="inner">
                        <h4 class="mb-0" style="font-size: 24px;">Date-to-Date Sales</h4>
                    </div>
                    <div class="icon">
                        <i class="ion ion-ios-stopwatch-outline" style="font-size: 36px;"></i>
                    </div>
                </a>
            </div>
        </div>
        
        <!-- Row for Purchase -->
        <div class="row">
            <!-- Daily Purchase -->
            <div class="col-lg-3 col-md-6 mb-4">
                <a href="{{ route('reports.dailyPurchase') }}" class="small-box bg-danger d-block text-center p-3 text-white rounded">
                    <div class="inner">
                        <h4 class="mb-0" style="font-size: 24px;">Daily Purchase</h4>
                    </div>
                    <div class="icon">
                        <i class="fas fa-coins" style="font-size: 36px;"></i>
                    </div>
                </a>
            </div>
            
            <!-- Weekly Purchase -->
            <div class="col-lg-3 col-md-6 mb-4">
                <a href="{{ route('reports.weeklyPurchase') }}" class="small-box bg-secondary d-block text-center p-3 text-white rounded">
                    <div class="inner">
                        <h4 class="mb-0" style="font-size: 24px;">Weekly Purchase</h4>
                    </div>
                    <div class="icon">
                        <i class="fas fa-shopping-cart" style="font-size: 36px;"></i>
                    </div>
                </a>
            </div>
            
            <!-- Monthly Purchase -->
            <div class="col-lg-3 col-md-6 mb-4">
                <a href="{{ route('reports.monthlyPurchase') }}" class="small-box bg-success d-block text-center p-3 text-white rounded">
                    <div class="inner">
                        <h4 class="mb-0" style="font-size: 24px;">Monthly Purchase</h4>
                    </div>
                    <div class="icon">
                        <i class="ion ion-bag" style="font-size: 36px;"></i>
                    </div>
                </a>
            </div>
            
            <!-- Date-to-Date Purchase -->
            <div class="col-lg-3 col-md-6 mb-4">
                <a href="{{ route('reports.dateToDatePurchase') }}" class="small-box bg-info d-block text-center p-3 text-white rounded">
                    <div class="inner">
                        <h4 class="mb-0" style="font-size: 24px;">Date-to-Date Purchases</h4>
                    </div>
                    <div class="icon">
                        <i class="ion ion-ios-timer-outline" style="font-size: 36px;"></i>
                    </div>
                </a>
            </div>

            <div class="col-lg-3 col-md-6 mb-4">
                <a href="{{ route('allstock') }}" class="small-box bg-primary d-block text-center p-3 text-white rounded">
                    <div class="inner">
                        <h4 class="mb-0" style="font-size: 24px;">Stocks</h4>
                    </div>
                    <div class="icon">
                        <i class="ion ion-ios-analytics-outline" style="font-size: 36px;"></i>
                    </div>
                </a>
            </div>


        </div>
    </div>
</section>

@endsection

@section('script')

@endsection
