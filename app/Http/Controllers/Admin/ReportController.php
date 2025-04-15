<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use DataTables;
use Illuminate\Support\Carbon;
use App\Models\Purchase;

class ReportController extends Controller
{
    public function index()
    {

        if (!(in_array('29', json_decode(auth()->user()->role->permission)))) {
          return redirect()->back()->with('error', 'Sorry, You do not have permission to access that page.');
        }
        return view('admin.reports.index');
    }

    public function dailySale()
    {
        return view('admin.reports.daily_sale');
    }

    public function dailySalesDataTable()
    {
        $orders = Order::with('user')
            ->whereDate('purchase_date', today())
            ->orderBy('id', 'desc');

        return DataTables::of($orders)
            ->addColumn('purchase_date', function ($order) {
                return Carbon::parse($order->purchase_date)->format('d-m-Y');
            })
            ->addColumn('status', function ($order) {
                $statusLabels = [
                    1 => 'Pending',
                    2 => 'Processing',
                    3 => 'Packed',
                    4 => 'Shipped',
                    5 => 'Delivered',
                    6 => 'Returned',
                    7 => 'Cancelled'
                ];
                return $statusLabels[$order->status] ?? 'Unknown';
            })
            ->addColumn('action', function ($order) {
                return '<a href="'.route('admin.orders.details', ['orderId' => $order->id]).'" class="btn btn-sm btn-info">Details</a>';
            })
            ->addColumn('user_details', function ($order) {
                $name = $order->user->name ?? '';
                $email = $order->user->email ?? '';
                $phone = $order->user->phone ?? '';
                
                return trim("$name, $email, $phone", ', ');
            })
            ->editColumn('name', function ($order) {
                return "{$order->name}<br>{$order->email}<br>{$order->phone}";
            })
            ->rawColumns(['name', 'action'])
            ->make(true);
    }

    public function weeklySale()
    {
        return view('admin.reports.weekly_sale');
    }

    public function weeklySalesDataTable()
    {
        $endDate = Carbon::now()->endOfDay();
        $startDate = Carbon::now()->subDays(6)->startOfDay();

        $orders = Order::with('user')
            ->whereBetween('purchase_date', [$startDate, $endDate])
            ->orderBy('id', 'desc');

        return DataTables::of($orders)
            ->addColumn('purchase_date', function ($order) {
                return Carbon::parse($order->purchase_date)->format('d-m-Y');
            })
            ->addColumn('status', function ($order) {
                $statusLabels = [
                    1 => 'Pending',
                    2 => 'Processing',
                    3 => 'Packed',
                    4 => 'Shipped',
                    5 => 'Delivered',
                    6 => 'Returned',
                    7 => 'Cancelled'
                ];
                return $statusLabels[$order->status] ?? 'Unknown';
            })
            ->addColumn('user_details', function ($order) {
                $name = $order->user->name ?? '';
                $email = $order->user->email ?? '';
                $phone = $order->user->phone ?? '';
                
                return trim("$name, $email, $phone", ', ');
            })
            ->addColumn('action', function ($order) {
                return '<a href="'.route('admin.orders.details', ['orderId' => $order->id]).'" class="btn btn-sm btn-info">Details</a>';
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    public function monthlySale()
    {
        return view('admin.reports.monthly_sale');
    }

    public function monthlySalesDataTable()
    {
        $endDate = Carbon::now()->endOfDay();
        $startDate = Carbon::now()->subDays(29)->startOfDay();

        $orders = Order::with('user')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->orderBy('id', 'desc');

        return DataTables::of($orders)
            ->addColumn('purchase_date', function ($order) {
                return Carbon::parse($order->purchase_date)->format('d-m-Y');
            })
            ->addColumn('user_details', function ($order) {
                $name = $order->user->name ?? '';
                $email = $order->user->email ?? '';
                $phone = $order->user->phone ?? '';
                
                return trim("$name, $email, $phone", ', ');
            })
            
            ->addColumn('status', function ($order) {
                $statusLabels = [
                    1 => 'Pending',
                    2 => 'Processing',
                    3 => 'Packed',
                    4 => 'Shipped',
                    5 => 'Delivered',
                    6 => 'Returned',
                    7 => 'Cancelled'
                ];
                return $statusLabels[$order->status] ?? 'Unknown';
            })
            ->addColumn('action', function ($order) {
                return '<a href="'.route('admin.orders.details', ['orderId' => $order->id]).'" class="btn btn-sm btn-info">Details</a>';
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    public function dailyPurchase()
    {
        return view('admin.reports.daily_purchase');
    }

    public function dailyPurchasesDataTable(Request $request)
    {
        $dailyPurchases = Purchase::select([
            'id',
            'purchase_date',
            'invoice',
            'supplier_id',
            'total_amount',
            'paid_amount',
            'due_amount'
        ])
        ->with('supplier')
        ->whereDate('purchase_date', today())
        ->get();

        return DataTables::of($dailyPurchases)
            ->addColumn('purchase_date', function ($purchase) {
                return Carbon::parse($purchase->purchase_date)->format('d-m-Y');
            })
            ->addColumn('supplier_name', function ($purchase) {
                return $purchase->supplier ? $purchase->supplier->name : 'Unknown Supplier';
            })
            ->make(true);
    }

    public function weeklyPurchase()
    {
        return view('admin.reports.weekly_purchase');
    }

    public function weeklyPurchasesDataTable(Request $request)
    {
        $endDate = Carbon::now()->endOfDay();
        $startDate = Carbon::now()->subDays(6)->startOfDay();

        $weeklyPurchases = Purchase::select([
            'id',
            'purchase_date',
            'invoice',
            'supplier_id',
            'total_amount',
            'paid_amount',
            'due_amount'
        ])
        ->with('supplier')
        ->whereBetween('purchase_date', [$startDate, $endDate])
        ->orderBy('id', 'desc')
        ->get();

        return DataTables::of($weeklyPurchases)
            ->addColumn('purchase_date', function ($purchase) {
                return Carbon::parse($purchase->purchase_date)->format('d-m-Y');
            })
            ->addColumn('supplier_name', function ($purchase) {
                return $purchase->supplier ? $purchase->supplier->name : 'Unknown Supplier';
            })
            ->make(true);
    }
    
    public function monthlyPurchase()
    {
        return view('admin.reports.monthly_purchase');
    }

    public function monthlyPurchasesDataTable(Request $request)
    {
        $endDate = Carbon::now()->endOfDay();
        $startDate = Carbon::now()->subDays(29)->startOfDay();

        $monthlyPurchases = Purchase::select([
            'id',
            'purchase_date',
            'invoice',
            'supplier_id',
            'total_amount',
            'paid_amount',
            'due_amount'
        ])
        ->with('supplier')
        ->whereBetween('purchase_date', [$startDate, $endDate])
        ->orderBy('id', 'desc')
        ->get();

        return DataTables::of($monthlyPurchases)
            ->addColumn('purchase_date', function ($purchase) {
                return Carbon::parse($purchase->purchase_date)->format('d-m-Y');
            })
            ->addColumn('supplier_name', function ($purchase) {
                return $purchase->supplier ? $purchase->supplier->name : 'Unknown Supplier';
            })
            ->make(true);
    }

    public function dateToDateSale()
    {
        return view('admin.reports.date_to_date_sale');
    }

    public function dateToDateSalesDataTable(Request $request)
    {
        $startDate = $request->start_date;
        $endDate = $request->end_date;

        $orders = Order::query()
            ->when($startDate, function ($query, $startDate) {
                return $query->whereDate('purchase_date', '>=', $startDate);
            })
            ->when($endDate, function ($query, $endDate) {
                return $query->whereDate('purchase_date', '<=', $endDate);
            })
            ->select([
                'purchase_date',
                'invoice',
                'name',
                'email',
                'phone',
                'subtotal_amount',
                'shipping_amount',
                'discount_amount',
                'net_amount',
                'payment_method',
                'status',
                'id'
            ])
            ->orderBy('id', 'desc');

        return DataTables::of($orders)
            ->addColumn('purchase_date', function ($order) {
                return Carbon::parse($order->purchase_date)->format('d-m-Y');
            })
            ->addColumn('action', function ($order) {
                return '<a href="'.route('admin.orders.details', ['orderId' => $order->id]).'" class="btn btn-sm btn-info">Details</a>';
            })
            ->addColumn('status', function ($order) {
                $statusLabels = [
                    1 => 'Pending',
                    2 => 'Processing',
                    3 => 'Packed',
                    4 => 'Shipped',
                    5 => 'Delivered',
                    6 => 'Returned',
                    7 => 'Cancelled'
                ];
                return $statusLabels[$order->status] ?? 'Unknown';
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    public function dateToDatePurchase()
    {
        return view('admin.reports.date_to_date_purchase');
    }

    public function dateToDatePurchasesDataTable(Request $request)
    {
        $startDate = $request->start_date;
        $endDate = $request->end_date;

        $purchasesQuery = Purchase::select([
                'id', 
                'purchase_date', 
                'invoice', 
                'supplier_id', 
                'total_amount', 
                'paid_amount', 
                'due_amount'
            ])
            ->with('supplier')
            ->when($startDate, function ($query) use ($startDate) {
                return $query->whereDate('purchase_date', '>=', $startDate);
            })
            ->when($endDate, function ($query) use ($endDate) {
                return $query->whereDate('purchase_date', '<=', $endDate);
            })
            ->orderBy('id', 'desc');

        return DataTables::of($purchasesQuery)
            ->addColumn('purchase_date', function ($purchase) {
                return Carbon::parse($purchase->purchase_date)->format('d-m-Y');
            })
            ->addColumn('supplier_name', function ($purchase) {
                return $purchase->supplier ? $purchase->supplier->name : 'Unknown Supplier';
            })
            ->make(true);
    }

}
