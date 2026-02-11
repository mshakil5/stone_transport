<?php

namespace App\Http\Controllers;

use App\Models\Color;
use App\Models\OrderDetails;
use Illuminate\Http\Request;
use App\Models\Stock;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\Purchase;
use Illuminate\Support\Facades\Auth;
use App\Models\PurchaseHistory;
use Illuminate\Support\Facades\DB;
use App\Models\PurchaseReturn;
use DataTables;
use App\Models\SystemLose;
use App\Models\OrderReturn;
use App\Models\Size;
use App\Models\StockHistory;
use App\Models\SupplierTransaction;
use App\Models\Transaction;
use App\Models\Warehouse;
use Carbon\Carbon;
use App\Models\MotherVassel;
use App\Models\LighterVassel;
use App\Models\Ghat;
use App\Models\ChartOfAccount;

class StockController extends Controller
{
    public function getStock()
    {

        if (!(in_array('16', json_decode(auth()->user()->role->permission)))) {
          return redirect()->back()->with('error', 'Sorry, You do not have permission to access that page.');
        }
        $products = Product::select('id','name','product_code')->orderBy('id', 'DESC')->get();
        $warehouses = Warehouse::select('id', 'name','location')->where('status', 1)->get();
        return view('admin.stock.index', compact('warehouses','products'));
    }

    public function getStocks(Request $request)
    {
        // $query = Stock::query();
        $query = Stock::select('product_id', 'warehouse_id', 'size','color', 'quantity');
        if ($request->has('warehouse_id') && $request->warehouse_id != '') {
            $query->where('warehouse_id', $request->warehouse_id);
        }

        if ($request->has('product_id') && $request->product_id != '') {
            $query->where('product_id', $request->product_id);
        }
       $data = $query->orderBy('id', 'DESC')->get();
       
        return DataTables::of($data)
            ->addColumn('sl', function($row) {
                static $i = 1;
                return $i++;
            })
            ->addColumn('product_name', function ($row) {
                return $row->product ? $row->product->name : 'N/A';
            })
            ->addColumn('warehouse', function ($row) {
                return $row->warehouse ? $row->warehouse->name : 'N/A';
            })
            ->addColumn('product_code', function ($row) {
                return $row->product ? $row->product->product_code : 'N/A';
            })
            // ->addColumn('warehouse', function ($row) {
            //     $warehouseDtl = '<b>'.$row->warehouse ? $row->warehouse->name .'-'. $row->warehouse->location : 'N/A'.'</b>';
            //     return $warehouseDtl;
            // })
            // ->addColumn('action', function ($row) {
            // return '<button class="btn btn-sm btn-danger" onclick="openLossModal('.$row->id.')">System Loss</button>';
            // })
            ->addColumn('action', function ($data) {
                $btn = '<div class="table-actions"> <button class="btn btn-sm btn-danger btn-open-loss-modal" data-size="'.$data->size.'" data-color="'.$data->color.'" data-warehouse="'.$data->warehouse_id.'" data-id="'.$data->product->id.'" >System Loss</button> ';  
                if (Auth::user()) {
                    $url = route('admin.product.purchasehistory', ['id' => $data->product->id,'warehouse_id' => $data->warehouse_id]);
                    $btn .= '<a href="'.$url.'" class="btn btn-sm btn-primary">History</a>';
                }
                $btn .= '</div>';
                return $btn;
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    public function stockingHistory()
    {
        $products = Product::select('id','name','product_code')->orderBy('id', 'DESC')->get();
        $warehouses = Warehouse::select('id', 'name','location')->where('status', 1)->get();

        return view('admin.stock.stockhistory', compact('warehouses','products'));
    }

    public function getStockingHistory(Request $request)
    {
        
        
        $query = StockHistory::select('date', 'stockid', 'purchase_id', 'product_id', 'stock_id', 'warehouse_id', 'quantity', 'selling_qty','available_qty', 'size','color','systemloss_qty','purchase_price','selling_price', 'systemloss_qty', 'mother_vassels_id',);
        if ($request->has('warehouse_id') && $request->warehouse_id != '') {
            $query->where('warehouse_id', $request->warehouse_id);
        }
        if ($request->has('product_id') && $request->product_id != '') {
            $query->where('product_id', $request->product_id);
        }
        $data = $query->orderBy('available_qty', 'DESC')->get();

        return DataTables::of($data)
            ->addColumn('sl', function($row) {
                static $i = 1;
                return $i++;
            })
            ->addColumn('date', function ($row) {
                return $row->date ? Carbon::parse($row->date)->format('d-m-Y') : 'N/A';
            })
            ->addColumn('product_info', function ($row) {
                if ($row->product) {
                    return $row->product->product_code . '-' . $row->product->name;
                }
                return 'N/A';
            })
    
            ->addColumn('quantity_formatted', function ($row) {
                return $row->quantity ? number_format($row->quantity, 0) : ' ';
            })
            
            ->addColumn('selling_qty', function ($row) {
                return $row->selling_qty ? $row->selling_qty : ' ';
            })

            ->addColumn('selling_price', function ($row) {
                return $row->selling_price ? $row->selling_price : ' ';
            })
            
            ->addColumn('available_qty', function ($row) {
                return $row->available_qty ? $row->available_qty : '';
            })
            ->addColumn('systemloss_qty', function ($row) {
                return $row->systemloss_qty ? $row->systemloss_qty : '';
            })
            
            ->addColumn('purchase_price', function ($row) {
                return $row->purchase_price ? $row->purchase_price : '';
                // return json_encode($row);
            })

            ->addColumn('mother_Vessel', function ($row) {
                return $row->motherVessel ? $row->motherVessel->name : '';
            })

            ->addColumn('warehouse', function ($row) {
                return $row->warehouse ? $row->warehouse->name : '';
            })
            ->addColumn('action', function ($data) {
                $btn = '<div class="table-actions"> ';  
                if (Auth::user()) {
                    $url = route('admin.product.purchasehistory', ['id' => $data->product->id, 'size' => $data->size, 'color' => $data->color]);
                    $btn .= '<a href="'.$url.'" class="btn btn-sm btn-primary">History</a>';
                }
                $btn .= '</div>';
                return $btn;
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    public function getStockLedger()
    {
        if (!(in_array('17', json_decode(auth()->user()->role->permission)))) {
          return redirect()->back()->with('error', 'Sorry, You do not have permission to access that page.');
        }

        $totalQty = Stock::sum('quantity');
        $totalProduct = Stock::count('product_id');
        $totalSupplier = Supplier::count();

        $products = Product::select('id','name','product_code')->orderBy('id', 'DESC')->get();
        $warehouses = Warehouse::select('id', 'name','location')->where('status', 1)->get();
        return view('admin.stock.stockledger', compact('warehouses','products','totalQty','totalProduct'));
    }

    public function getproductHistory()
    {
        $today = Carbon::today();
        $yesterday = Carbon::yesterday();

        $products = DB::table('products')
                        ->leftJoin('purchase_histories', 'products.id', '=', 'purchase_histories.product_id')
                        ->leftJoin('stock_histories', 'products.id', '=', 'stock_histories.product_id')
                        ->leftJoin('order_details', 'products.id', '=', 'order_details.product_id')
                        ->leftJoin('order_returns', 'products.id', '=', 'order_returns.product_id')
                        ->leftJoin('purchase_returns', 'products.id', '=', 'purchase_returns.product_id')
                        ->select('products.id', 'products.name',
                            DB::raw("SUM(CASE WHEN purchase_histories.created_at <= '{$yesterday}' THEN purchase_histories.quantity ELSE 0 END) as previous_day_purchase"),
                            DB::raw("SUM(CASE WHEN purchase_histories.created_at = '{$today}' THEN purchase_histories.quantity ELSE 0 END) as today_purchase_qty"),
                            DB::raw("SUM(CASE WHEN stock_histories.created_at <= '{$yesterday}' THEN stock_histories.quantity ELSE 0 END) as previous_day_stock"),
                            DB::raw("SUM(CASE WHEN stock_histories.created_at = '{$today}' THEN stock_histories.quantity ELSE 0 END) as today_stock_qty"),
                            DB::raw("SUM(CASE WHEN order_details.created_at = '{$today}' THEN order_details.quantity ELSE 0 END) as today_sales_qty"),
                            DB::raw("SUM(CASE WHEN order_details.created_at <= '{$yesterday}' THEN order_details.quantity ELSE 0 END) as previous_sales_qty"),
                            DB::raw("SUM(CASE WHEN purchase_returns.created_at = '{$today}' THEN purchase_returns.return_quantity ELSE 0 END) as today_purchase_return_qty"),
                            DB::raw("SUM(CASE WHEN purchase_returns.created_at <= '{$yesterday}' THEN purchase_returns.return_quantity ELSE 0 END) as previous_purchase_return_qty"),
                            DB::raw("SUM(CASE WHEN order_returns.created_at = '{$today}' THEN order_returns.quantity ELSE 0 END) as today_sales_return_qty"),
                            DB::raw("SUM(CASE WHEN order_returns.created_at <= '{$yesterday}' THEN order_returns.quantity ELSE 0 END) as previous_sales_return_qty")
                        )
                        ->groupBy('products.id', 'products.name')
                        ->get();
                        
        return view('admin.stock.getproducthistory', compact('products'));
    }

    
    public function getsingleProductHistory(Request $request, $id, $warehouse_id = null)
    {
        if ($request->fromDate || $request->toDate) {
            $request->validate([
                'fromDate' => 'nullable|date', 
                'toDate' => 'required_with:fromDate|date|after_or_equal:fromDate', 
            ]);

            $fromDate = Carbon::parse($request->input('fromDate'))->startOfDay();
            $toDate = Carbon::parse($request->input('toDate'))->endOfDay();   
        }else{
            $fromDate = '';
            $toDate = '';
        }
        
        $product = Product::select('id', 'name','product_code')->where('id', $id)->first();
        $warehouses = Warehouse::where('status', 1)->get();

        $purchaseHistories = PurchaseHistory::where('product_id', $id)
                            ->when($fromDate, function ($query) use ($fromDate, $toDate) {
                                $query->whereBetween('created_at', [$fromDate, $toDate]);
                            })
                            // ->where('product_size', $size)
                            // ->where('product_color', $color)
                            ->orderby('id','DESC')
                            ->get();

        $salesHistories = OrderDetails::where('product_id', $id)
                            ->when($fromDate, function ($query) use ($fromDate, $toDate) {
                                $query->whereBetween('created_at', [$fromDate, $toDate]);
                            })
                            ->when($request->input('warehouse_id'), function ($query) use ($request) {
                                $query->where("warehouse_id",$request->input('warehouse_id'));
                            })
                            // ->where('size', $size)
                            // ->where('color', $color)
                            ->orderby('id','DESC')
                            ->whereHas('order', function ($query) {
                                $query->whereIn('order_type', ['0','1']);
                            })->get();


        return view('admin.stock.single_product_history', compact('purchaseHistories','salesHistories','product','warehouses', 'id', 'warehouse_id'));
    }

    public function addstock()
    {

        $products = Product::orderby('id','DESC')->select('id', 'name','price', 'product_code')->get();
        $suppliers = Supplier::where('status', 1)->select('id', 'name')->orderby('id','DESC')->get();
        $colors = Color::where('status', 1)->select('id', 'color')->orderby('id','DESC')->get();
        $sizes = Size::where('status', 1)->select('id', 'size')->orderby('id','DESC')->get();
        $warehouses = Warehouse::where('status', 1)->select('id', 'name','location')->orderby('id','DESC')->get();
        $motherVassels = MotherVassel::select('id', 'name','code')->orderby('id','DESC')->get();
        $lighterVassels = LighterVassel::select('id', 'name','code')->orderby('id','DESC')->get();
        $ghats = Ghat::select('id', 'name','code')->orderby('id','DESC')->get();
        return view('admin.stock.create', compact('products', 'suppliers', 'colors', 'sizes','warehouses','motherVassels','lighterVassels','ghats'));
    }

    public function stockStore(Request $request)
    {

        $validatedData = $request->validate([
            'invoice' => 'required',
            'supplier_id' => 'required|exists:suppliers,id',
            'mother_vassels_id' => 'required',
            'lighter_vassels_id' => 'required',
            'ghats_id' => 'required',
            // 'warehouse_id' => 'required|exists:warehouses,id',
            'purchase_date' => 'required|date',
            // 'purchase_type' => 'required',
            'ref' => 'nullable|string',
            'vat_reg' => 'nullable|string',
            'remarks' => 'nullable|string',
            'total_amount' => 'required|numeric',
            'discount' => 'nullable|numeric',
            'total_vat_amount' => 'required|numeric',
            'net_amount' => 'required|numeric',
            'due_amount' => 'required|numeric',
            'products' => 'required|array',
            'products.*.product_id' => 'required|exists:products,id',
            'products.*.quantity' => 'required|numeric|min:1',
            // 'products.*.product_size' => 'nullable|string',
            // 'products.*.product_color' => 'nullable|string',
            'products.*.unit_price' => 'required|numeric',
            'products.*.vat_percent' => 'nullable|numeric',
            'products.*.vat_amount' => 'nullable|numeric',
            'products.*.total_price_with_vat' => 'required|numeric',
        ]);

        $data = $request->all();
        $purchase = new Purchase();
        $purchase->invoice = $request->invoice;
        $purchase->supplier_id = $request->supplier_id;
        $purchase->mother_vassels_id = $request->mother_vassels_id;
        $purchase->lighter_vassels_id = $request->lighter_vassels_id;
        $purchase->ghats_id = $request->ghats_id;
        $purchase->purchase_date = $request->purchase_date;
        $purchase->purchase_type = $request->purchase_type;
        $purchase->ref = $request->ref;
        $purchase->vat_reg = $request->vat_reg;
        $purchase->remarks = $request->remarks;
        $purchase->total_amount = $request->total_amount;
        $purchase->discount = $request->discount;
        $purchase->direct_cost = $request->direct_cost;
        $purchase->cnf_cost = $request->cnf_cost;
        $purchase->cost_b = $request->cost_b;
        $purchase->cost_a = $request->cost_a;
        $purchase->other_cost = $request->other_cost;
        $purchase->total_vat_amount = $request->total_vat_amount;
        $purchase->net_amount = $request->net_amount;
        $purchase->paid_amount = ($request->cash_payment ?? 0) + ($request->bank_payment ?? 0);
        $purchase->due_amount = $request->net_amount - $request->cash_payment - $request->bank_payment;
        $purchase->created_by = Auth::user()->id;
        $purchase->save();

        if ($request->warehouse_id) {
            $purchase->status = 4;
            $purchase->save();
        }
        
        foreach ($request->products as $product) {
            $purchaseHistory = new PurchaseHistory();
            $purchaseHistory->purchase_id = $purchase->id;
            $purchaseHistory->product_id = $product['product_id'];
            $purchaseHistory->quantity = $product['quantity'];
            
            // $purchaseHistory->product_size = $product['product_size'];
            // $purchaseHistory->product_color = $product['product_color'];
            $purchaseHistory->purchase_price = $product['unit_price'];
            $purchaseHistory->vat_percent = $product['vat_percent'];
            $purchaseHistory->vat_amount_per_unit = $product['vat_amount'] / $product['quantity'];
            $purchaseHistory->total_vat = $purchaseHistory->vat_amount_per_unit * $product['quantity'];
            $purchaseHistory->total_amount = $product['unit_price'] * $product['quantity'];
            $purchaseHistory->total_amount_with_vat = $product['total_price_with_vat'];
            if ($request->warehouse_id) {
                $purchaseHistory->remaining_product_quantity = 0;
                $purchaseHistory->transferred_product_quantity = $product['quantity'];
            }else{
                $purchaseHistory->remaining_product_quantity = $product['quantity'];
                $purchaseHistory->transferred_product_quantity = 0;
            }

            $purchaseHistory->created_by = Auth::user()->id;
            $purchaseHistory->save();

            $existingProduct = Product::find($product['product_id']);
            if ($existingProduct){   
                $existingProduct->price = $product['unit_price'];
                $existingProduct->save();
            }

            if ($request->warehouse_id) {
                $stock = Stock::where('product_id', $product['product_id'])
                    //   ->where('size', $product['product_size'])
                    //   ->where('color', $product['product_color'])
                      ->where('warehouse_id', $request->warehouse_id)
                      ->first();
                if ($stock) {
                    $stock->quantity += $product['quantity'];
                    $stock->updated_by = Auth::user()->id;
                    $stock->save();
                } else {
                    $newStock = new Stock();
                    $newStock->warehouse_id = $request->warehouse_id;
                    $newStock->product_id = $product['product_id'];
                    $newStock->quantity = $product['quantity'];
                    // $newStock->size = $product['product_size'];
                    // $newStock->color = $product['product_color'];
                    $newStock->created_by = Auth::user()->id;
                    $newStock->save();
                }
                // calculate every additional cost per product
                $additionalCost = $purchase->direct_cost + $purchase->cnf_cost + $purchase->cost_a + $purchase->cost_b + $purchase->other_cost;
                $qty = $purchaseHistory->quantity - $purchaseHistory->missing_product_quantity;
                $countItem = Purchase::withCount('purchaseHistory')->where('id', $purchaseHistory->purchase_id)->first();
                $additionalCostPerProduct = $additionalCost/$countItem->purchase_history_count;
                $additionalCostPerUnit = $additionalCostPerProduct/$qty;
                // calculate every additional cost per product

                $warehouseId = $request->warehouse_id;
                $stockhistory = new StockHistory();
                $stockhistory->product_id = $purchaseHistory->product_id;
                $stockhistory->purchase_id = $purchaseHistory->purchase_id;
                if ($stock) {
                    $stockhistory->stock_id = $stock->id;
                } else {
                    $stockhistory->stock_id = $newStock->id;
                }
                
                $stockhistory->warehouse_id = $warehouseId;
                $stockhistory->selling_qty = 0;
                $stockhistory->quantity = $product['quantity'];
                $stockhistory->available_qty = $product['quantity'];
                // $stockhistory->size = $purchaseHistory->product_size;
                // $stockhistory->color = $purchaseHistory->product_color;
                $stockhistory->date = date('Y-m-d');
                $stockhistory->stockid = date('md').$warehouseId.str_pad($purchaseHistory->id, 4, '0', STR_PAD_LEFT);

                $stockhistory->purchase_price = $purchaseHistory->purchase_price + $additionalCostPerUnit;
                $stockhistory->selling_price = $stockhistory->purchase_price + $stockhistory->purchase_price * .2;
                $stockhistory->created_by = Auth::user()->id;
                $stockhistory->save();
            }
        }

        $suppliertran = new Transaction();
        $suppliertran->date = $request->purchase_date;
        $suppliertran->supplier_id = $request->supplier_id;
        $suppliertran->purchase_id = $purchase->id;
        $suppliertran->table_type = "Purchase";
        $suppliertran->description = "Purchase";
        $suppliertran->payment_type = "Credit";
        $suppliertran->transaction_type = "Due";
        $suppliertran->amount = $request->total_amount;
        $suppliertran->additional_cost = $request->direct_cost + $request->cnf_cost + $request->cost_b + $request->cost_a + $request->other_cost;
        $suppliertran->vat_amount = $request->total_vat_amount;
        $suppliertran->discount = $request->discount ?? 0.00;
        $suppliertran->at_amount = $request->net_amount;
        $suppliertran->save();
        $suppliertran->tran_id = 'SL' . date('ymd') . str_pad($suppliertran->id, 4, '0', STR_PAD_LEFT);
        $suppliertran->save();
        
        if ($request->cash_payment) {
            $cashpayment = new Transaction();
            $cashpayment->date = $request->purchase_date;
            $cashpayment->supplier_id = $request->supplier_id;
            $cashpayment->purchase_id = $purchase->id;
            $cashpayment->table_type = "Purchase";
            $cashpayment->description = "Purchase";
            $cashpayment->payment_type = "Cash";
            $cashpayment->transaction_type = "Current";
            $cashpayment->amount = $request->cash_payment;
            $cashpayment->at_amount = $request->cash_payment;
            $cashpayment->save();
            $cashpayment->tran_id = 'SL' . date('ymd') . str_pad($cashpayment->id, 4, '0', STR_PAD_LEFT);
            $cashpayment->save();
        }

        if ($request->bank_payment) {
            $bankpayment = new Transaction();
            $bankpayment->date = $request->purchase_date;
            $bankpayment->supplier_id = $request->supplier_id;
            $bankpayment->purchase_id = $purchase->id;
            $bankpayment->table_type = "Purchase";
            $bankpayment->description = "Purchase";
            $bankpayment->payment_type = "Bank";
            $bankpayment->transaction_type = "Current";
            $bankpayment->amount = $request->bank_payment;
            $bankpayment->at_amount = $request->bank_payment;
            $bankpayment->save();
            $bankpayment->tran_id = 'SL' . date('ymd') . str_pad($bankpayment->id, 4, '0', STR_PAD_LEFT);
            $bankpayment->save();
        }

        

        return response()->json([
            'status' => 'success',
            'message' => 'Purchased Successfully',
        ]);
    }

    public function purchaseTransaction(Request $request, $purchase)
    {
        $supplier = new Transaction();
        $supplier->date = $request->purchase_date;
        $supplier->supplier_id = $request->supplier_id;
        $supplier->purchase_id = $purchase->id;
        $supplier->table_type = "Purchase";
        $supplier->description = "Purchase";
        $supplier->payment_type = "Credit";
        $supplier->transaction_type = "Due";
        $supplier->amount = $request->total_amount;
        $supplier->vat_amount = $request->total_vat_amount;
        $supplier->discount = $request->discount ?? 0.00;
        $supplier->at_amount = $request->net_amount;
        $supplier->save();
        $supplier->tran_id = 'SL' . date('ymd') . str_pad($supplier->id, 4, '0', STR_PAD_LEFT);
        $supplier->save();

        if ($request->cash_payment) {
            $cashpayment = new Transaction();
            $cashpayment->date = $request->purchase_date;
            $cashpayment->supplier_id = $request->supplier_id;
            $cashpayment->purchase_id = $purchase->id;
            $cashpayment->table_type = "Purchase";
            $cashpayment->description = "Purchase";
            $cashpayment->payment_type = "Cash";
            $cashpayment->transaction_type = "Current";
            $cashpayment->amount = $request->cash_payment;
            $cashpayment->total_amount = $request->cash_payment;
            $cashpayment->save();
            $cashpayment->tran_id = 'SL' . date('ymd') . str_pad($cashpayment->id, 4, '0', STR_PAD_LEFT);
            $cashpayment->save();
        }

        if ($request->bank_payment) {
            $bankpayment = new Transaction();
            $bankpayment->date = $request->purchase_date;
            $bankpayment->supplier_id = $request->supplier_id;
            $bankpayment->purchase_id = $purchase->id;
            $bankpayment->table_type = "Purchase";
            $bankpayment->description = "Purchase";
            $bankpayment->payment_type = "Bank";
            $bankpayment->transaction_type = "Current";
            $bankpayment->amount = $request->bank_payment;
            $bankpayment->total_amount = $request->bank_payment;
            $bankpayment->save();
            $bankpayment->tran_id = 'SL' . date('ymd') . str_pad($bankpayment->id, 4, '0', STR_PAD_LEFT);
            $bankpayment->save();
        }

        return;

    }

    public function productPurchaseHistory()
    {
        if (!(in_array('10', json_decode(auth()->user()->role->permission)))) {
          return redirect()->back()->with('error', 'Sorry, You do not have permission to access that page.');
        }

        $purchases = Purchase::whereNotNull('invoice')->with('purchaseHistory.product','supplier')->orderby('id','DESC')->get();
        return view('admin.stock.purchase_history', compact('purchases'));
    }

    public function getPurchaseHistory(Purchase $purchase)
    {
        $purchase = Purchase::with(['supplier', 'motherVessel','transactions', 'purchaseHistory.product', 'purchaseHistory.lighterVessel',  'purchaseHistory.warehouse', 'purchaseHistory.ghat'])->findOrFail($purchase->id);
    
        return response()->json($purchase);
    }

    public function editPurchaseHistory(Purchase $purchase)
    {
        $purchase = Purchase::with('supplier', 'purchaseHistory.product', 'transactions')->findOrFail($purchase->id);
        $products = Product::orderby('id','DESC')->get();
        $suppliers = Supplier::orderby('id','DESC')->where('status', 1)->get();
        $warehouses = Warehouse::select('id', 'name','location')->where('status', 1)->get();
        $cashAmount = $purchase->transactions->where('payment_type', 'Cash')->first();
        $bankAmount = $purchase->transactions->where('payment_type', 'Bank')->first();
        $colors = Color::where('status', 1)->select('id', 'color')->orderby('id','DESC')->get();
        $sizes = Size::where('status', 1)->select('id', 'size')->orderby('id','DESC')->get();
        $motherVassels = MotherVassel::select('id', 'name','code')->orderby('id','DESC')->get();
        $lighterVassels = LighterVassel::select('id', 'name','code')->orderby('id','DESC')->get();
        $ghats = Ghat::select('id', 'name','code')->orderby('id','DESC')->get();
        $purchaseExpenses = Transaction::where('purchase_id', $purchase->id)
        ->where('table_type', 'Expenses')
        ->get();
        return view('admin.stock.edit_purchase_history', compact('purchase', 'products', 'suppliers', 'warehouses', 'cashAmount', 'bankAmount', 'colors', 'sizes','motherVassels','lighterVassels','ghats','purchaseExpenses'));
    }

    public function stockUpdate(Request $request)
    {
        DB::beginTransaction();
    
        $validatedData = $request->validate([
            'purchase_id' => 'required|exists:purchases,id',
            'supplier_id' => 'required|exists:suppliers,id',
            'mother_vassels_id' => 'required',
            'purchase_date' => 'required|date',
            'advance_date' => 'required|date',
            'purchase_type' => 'required',
            'ref' => 'nullable|string',
            'advance_amount' => 'nullable',
            'advance_quantity' => 'nullable',
            'vat_reg' => 'nullable|string',
            'remarks' => 'nullable|string',
            'total_amount' => 'required|numeric',
            'vat_percent' => 'nullable|numeric',
            'total_vat_amount' => 'nullable|numeric',
            'total_unloading_cost' => 'nullable|numeric',
            'discount' => 'nullable|numeric',
            'total_vat_amount' => 'required|numeric',
            'net_amount' => 'required|numeric',
            'due_amount' => 'required|numeric',
            'products' => 'required|array|min:1',
            'products.*.product_id' => 'required|exists:products,id',
            'products.*.quantity' => 'required|numeric|min:1',
            'products.*.lighter_vassel_id' => 'required',
            'products.*.warehouse_id' => 'required',
            'products.*.ghat_id' => 'required',
            'products.*.unloading_cost' => 'nullable',
            'products.*.unit_price' => 'required|numeric',
            'products.*.total_price' => 'required|numeric',
            'cash_payment' => 'nullable|numeric',
            'bank_payment' => 'nullable|numeric',
        ]);
    
        $purchase = Purchase::find($request->purchase_id);
    
        if (!$purchase) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Purchase not found.',
            ], 404);
        }
    
        if(!$purchase->invoice){
            $purchase->invoice = rand(10000000, 99999999);
        }
    
        $purchase->vat_percent = $request->vat_percent;
        $purchase->total_vat_amount = $request->total_vat_amount;
        $purchase->advance_quantity = $request->advance_quantity;
        $purchase->total_unloading_cost = $request->total_unloading_cost;
        $purchase->total_lighter_rent = $request->total_lighter_rent;
        $purchase->supplier_id = $request->supplier_id;
        $purchase->mother_vassels_id = $request->mother_vassels_id;
        $purchase->purchase_date = $request->purchase_date;
        $purchase->advance_date = $request->advance_date;
        $purchase->purchase_type = $request->purchase_type;
        $purchase->ref = $request->ref;
        $purchase->vat_reg = $request->vat_reg;
        $purchase->remarks = $request->remarks;
        $purchase->total_amount = $request->total_amount;
        $purchase->discount = $request->discount;
        $purchase->direct_cost = $request->direct_cost;
        $purchase->cnf_cost = $request->cnf_cost;
        $purchase->cost_b = $request->cost_b;
        $purchase->cost_a = $request->cost_a;
        $purchase->other_cost = $request->other_cost;
        $purchase->net_amount = $request->net_amount;
        $purchase->paid_amount = ($request->cash_payment ?? 0) + ($request->bank_payment ?? 0);
        $purchase->due_amount = $request->net_amount - $request->cash_payment - $request->bank_payment;
        $purchase->bill_number = $request->bill_number;
        $purchase->updated_by = Auth::user()->id;
        $purchase->save();
    
        // Handling Purchase History Updates
        $existingPurchaseHistoryIds = $purchase->purchaseHistory->pluck('id')->toArray();
        $updatedPurchaseHistoryIds = array_column($request->products, 'purchase_history_id');
    
        $removedPurchaseHistoryIds = array_diff($existingPurchaseHistoryIds, $updatedPurchaseHistoryIds);
    
        foreach ($removedPurchaseHistoryIds as $removedId) {
            $purchaseHistory = PurchaseHistory::find($removedId);
    
            if ($purchaseHistory) {
                $stockKey = $purchaseHistory->product_id . '_' . $purchaseHistory->warehouse_id;
    
                $stock = Stock::where('warehouse_id', $purchaseHistory->warehouse_id)
                              ->where('product_id', $purchaseHistory->product_id)
                              ->first();
    
                if ($stock) {
                    $stock->quantity = max($stock->quantity - $purchaseHistory->quantity, 0);
                    $stock->updated_by = Auth::id();
                    $stock->save();
    
                    $latestHistory = StockHistory::where('stock_id', $stock->id)
                                                ->latest('id')
                                                ->first();
    
                    if ($latestHistory) {
                        $latestHistory->quantity = max($latestHistory->quantity - $purchaseHistory->quantity, 0);
                        $latestHistory->available_qty = max($latestHistory->available_qty - $purchaseHistory->quantity, 0);
                        $latestHistory->updated_by = Auth::id();
                        $latestHistory->mother_vassels_id = $request->mother_vassels_id ?? null;
                        $latestHistory->save();
                    }
                }
    
                $purchaseHistory->delete();
            }
        }

        $totalCost = $request->total_unloading_cost + $request->total_lighter_rent;
        $totalQuantity = 0;
        foreach ($request->products as $product) {
          $newQty = is_numeric($product['quantity']) ? (float)$product['quantity'] : 0;
          $totalQuantity += $newQty;
        }
        $unitCost = $totalCost/$totalQuantity;

        foreach ($request->products as $product) {
            if (isset($product['purchase_history_id'])) {
                $purchaseHistory = PurchaseHistory::find($product['purchase_history_id']);
        
                if ($purchaseHistory) {
                    $oldQty = $purchaseHistory->quantity;
                    $qtyDiff = $newQty - $oldQty;
        
                    $stock = Stock::where('warehouse_id', $product['warehouse_id'])
                                  ->where('product_id', $product['product_id'])
                                  ->first();
        
                    if ($stock) {
                        $stock->quantity = max($stock->quantity + $qtyDiff, 0);
                        $stock->updated_by = Auth::id();
                        $stock->save();
        
                        $latestHistory = StockHistory::where('stock_id', $stock->id)->latest('id')->first();
                        if ($latestHistory) {
                            $latestHistory->quantity = max($latestHistory->quantity + $qtyDiff, 0);
                            $latestHistory->available_qty = max($latestHistory->available_qty + $qtyDiff, 0);
                            $latestHistory->unit_cost = $unitCost + $product['unit_price'];
                            $latestHistory->lighter_vassels_id = $product['lighter_vassel_id'] ?? null;
                            $latestHistory->mother_vassels_id = $request->mother_vassels_id ?? null;
                            $latestHistory->updated_by = Auth::id();
                            $latestHistory->save();
                        }
                    } else {
                        $newStock = new Stock();
                        $newStock->product_id = $product['product_id'];
                        $newStock->warehouse_id = $product['warehouse_id'];
                        $newStock->quantity = $newQty;
                        $newStock->created_by = Auth::id();
                        $newStock->save();
        
                        $newStockHistory = new StockHistory();
                        $newStockHistory->product_id = $product['product_id'];
                        $newStockHistory->warehouse_id = $product['warehouse_id'];
                        $newStockHistory->lighter_vassels_id = $product['lighter_vassel_id'];
                        $newStockHistory->mother_vassels_id = $request->mother_vassels_id;
                        $newStockHistory->date = date('Y-m-d');
                        $newStockHistory->stock_id = $newStock->id;
                        $newStockHistory->quantity = $newQty;
                        $newStockHistory->available_qty = $newQty;
                        $newStockHistory->created_by = Auth::id();
                        $newStockHistory->unit_cost = $unitCost + $product['unit_price'];
                        $newStockHistory->save();
                    }
        
                    $purchaseHistory->fill([
                        'quantity' => $newQty,
                        'purchase_price' => $product['unit_price'],
                        'vat_percent' => $request->vat_percent,
                        'vat_amount_per_unit' => $request->total_vat_amount / max($newQty, 1),
                        'total_vat' => $request->total_vat_amount,
                        'total_amount' => $product['unit_price'] * $newQty,
                        'total_amount_with_vat' => ($product['unit_price'] * $newQty) + $request->total_vat_amount,
                        'lighter_vassel_id' => $product['lighter_vassel_id'],
                        'warehouse_id' => $product['warehouse_id'],
                        'ghat_id' => $product['ghat_id'],
                        'unloading_cost' => $product['unloading_cost'],
                        'lighter_rent' => $product['lighter_rent'],
                        'quantity_type' => $product['quantity_type'],
                        'updated_by' => Auth::id(),
                    ]);
        
                    if ($purchaseHistory->transferred_product_quantity > 0) {
                        $purchaseHistory->remaining_product_quantity = 0;
                        $purchaseHistory->transferred_product_quantity = $newQty;
                    } else {
                        $purchaseHistory->remaining_product_quantity = $newQty;
                        $purchaseHistory->transferred_product_quantity = 0;
                    }
        
                    $purchaseHistory->save();
                }
            } else {
                $purchaseHistory = new PurchaseHistory();
                $purchaseHistory->fill([
                    'purchase_id' => $purchase->id,
                    'product_id' => $product['product_id'],
                    'quantity' => $newQty,
                    'purchase_price' => $product['unit_price'],
                    'vat_percent' => $request->vat_percent,
                    'vat_amount_per_unit' => $request->total_vat_amount / max($newQty, 1),
                    'total_vat' => $request->total_vat_amount,
                    'total_amount' => $product['unit_price'] * $newQty,
                    'total_amount_with_vat' => ($product['unit_price'] * $newQty) + $request->total_vat_amount,
                    'lighter_vassel_id' => $product['lighter_vassel_id'],
                    'warehouse_id' => $product['warehouse_id'],
                    'ghat_id' => $product['ghat_id'],
                    'lighter_rent' => $product['lighter_rent'],
                    'quantity_type' => $product['quantity_type'],
                    'unloading_cost' => $product['unloading_cost'],
                    'created_by' => Auth::id(),
                ]);
                $purchaseHistory->save();
        
                $stock = Stock::where('warehouse_id', $product['warehouse_id'])
                              ->where('product_id', $product['product_id'])
                              ->first();
        
                if ($stock) {
                    $stock->quantity = max($stock->quantity + $newQty, 0);
                    $stock->updated_by = Auth::id();
                    $stock->save();
        
                    $latestHistory = StockHistory::where('stock_id', $stock->id)->latest('id')->first();
        
                    if ($latestHistory) {
                        $latestHistory->quantity = max($latestHistory->quantity + $newQty, 0);
                        $latestHistory->available_qty = max($latestHistory->available_qty + $newQty, 0);
                        $latestHistory->updated_by = Auth::id();
                        $latestHistory->lighter_vassels_id = $product['lighter_vassel_id'] ?? null;
                        $latestHistory->mother_vassels_id = $request->mother_vassels_id ?? null;
                        $latestHistory->unit_cost = $unitCost + $product['unit_price'];
                        $latestHistory->save();
                    }
                } else {
                    $stock = new Stock();
                    $stock->warehouse_id = $product['warehouse_id'];
                    $stock->product_id = $product['product_id'];
                    $stock->quantity = $newQty;
                    $stock->updated_by = Auth::id();
                    $stock->save();
        
                    $newStockHistory = new StockHistory();
                    $newStockHistory->product_id = $product['product_id'];
                    $newStockHistory->warehouse_id = $product['warehouse_id'];
                    $newStockHistory->date = date('Y-m-d');
                    $newStockHistory->stock_id = $stock->id;
                    $newStockHistory->quantity = $newQty;
                    $newStockHistory->available_qty = $newQty;
                    $newStockHistory->created_by = Auth::id();
                    $newStockHistory->unit_cost = $unitCost + $product['unit_price'];
                    $newStockHistory->lighter_vassels_id = $product['lighter_vassel_id'] ?? null;
                    $newStockHistory->mother_vassels_id = $request->mother_vassels_id ?? null;
                    $newStockHistory->save();
                }
            }
        }

        DB::commit();
    
        return response()->json([
            'status' => 'success',
            'message' => 'Purchase updated successfully.',
        ]);
    }    

    public function returnProduct(Purchase $purchase)
    {
        $products = Product::orderby('id','DESC')->get();
        $suppliers = Supplier::orderby('id','DESC')->get();
        $purchase = Purchase::with('supplier', 'purchaseHistory.product')->findOrFail($purchase->id);
        return view('admin.stock.return_product', compact('purchase', 'products', 'suppliers'));
    }

    public function returnStore(Request $request)
    {

        $request->validate([
            'date' => 'required|date',
            'reason' => 'required|string|max:255',
            'supplierId' => 'required|exists:suppliers,id',
            'products' => 'required|array',
            'products.*.purchase_history_id' => 'required|exists:purchase_histories,id',
            'products.*.product_id' => 'required|exists:products,id',
            'products.*.return_quantity' => 'required|numeric|min:1',
        ]);

        DB::transaction(function () use ($request) {

            $date = $request->date;
            $reason = $request->reason;
            $supplierId = $request->supplierId;
            $products = $request->products;

            foreach ($products as $product) {
                $purchaseReturn = new PurchaseReturn();
                $purchaseReturn->date = $date;
                $purchaseReturn->reason = $reason;
                $purchaseReturn->supplier_id = $supplierId;
                $purchaseReturn->purchase_history_id = $product['purchase_history_id'];
                $purchaseReturn->product_id = $product['product_id'];
                $purchaseReturn->return_quantity = $product['return_quantity'];
                $purchaseReturn->status = 'pending'; 
                $purchaseReturn->created_by = auth()->user()->id;
                $purchaseReturn->save();

                $product_id = $product['product_id'];
                $return_quantity = $product['return_quantity'];
                
                $purchaseHistory = PurchaseHistory::find($product['purchase_history_id']);
    
                if (!$purchaseHistory) {
                    continue;
                }
    
                $purchaseHistory->remaining_product_quantity -= $product['return_quantity'];
                $purchaseHistory->save();

            }
        });

        return response()->json(['message' => 'Purchase return saved successfully'], 200);
    }

    public function stockReturnHistory()
    {

        if (!(in_array('11', json_decode(auth()->user()->role->permission)))) {
          return redirect()->back()->with('error', 'Sorry, You do not have permission to access that page.');
        }

        $purchaseReturns = PurchaseReturn::with('product', 'purchaseHistory') ->orderBy('id', 'desc')->get();
        return view('admin.stock.stock_return_history', compact('purchaseReturns'));
    }

    public function processSystemLoss(Request $request)
    {
        $validatedData = $request->validate([
            'productId' => 'required|exists:stocks,product_id', 
            'warehouse' => 'required', 
            'lossQuantity' => 'required|numeric|min:1', 
            'lossReason' => 'nullable|string|max:255',
        ]);

        $stock = Stock::where('product_id', $validatedData['productId'])->where('size',$request->size)->where('color',$request->color)->where('warehouse_id', $request->warehouse)->first();

        if (!$stock) {
            return response()->json(['message' => 'Stock record not found.'], 404);
        }
        if ($validatedData['lossQuantity'] > $stock->quantity) {
            return response()->json(['message' => 'Loss quantity cannot be more than current stock quantity.'], 422);
        }

        $newQuantity = $stock->quantity - $validatedData['lossQuantity'];
        $stock->update(['quantity' => $newQuantity]);

        $systemLoss = new SystemLose();
        $systemLoss->warehouse_id = $validatedData['warehouse'];
        $systemLoss->product_id = $validatedData['productId'];
        $systemLoss->quantity = $validatedData['lossQuantity'];
        $systemLoss->reason = $validatedData['lossReason'];
        $systemLoss->created_by = Auth::user()->id;
        $systemLoss->save();

        $history = StockHistory::where('product_id', $validatedData['productId'])
          ->where('warehouse_id', $validatedData['warehouse'])
          ->where('available_qty', '>=', $validatedData['lossQuantity'])
          ->orderBy('id')
          ->first();

      if ($history) {
          $history->available_qty -= $validatedData['lossQuantity'];
          $history->systemloss_qty += $validatedData['lossQuantity'];
          $history->save();
      }



        return response()->json(['message' => 'System loss processed successfully.']);
    }

    public function systemLosses()
    {

        if (!(in_array('19', json_decode(auth()->user()->role->permission)))) {
          return redirect()->back()->with('error', 'Sorry, You do not have permission to access that page.');
        }

        $systemLosses = SystemLose::with(['product', 'warehouse'])->latest()->get();
        return view('admin.stock.system_losses', compact('systemLosses'));
    }

    public function sendToStock(Request $request)
    {
        // dd($request->all());
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|numeric|min:1',
        ]);

        $product_id = $request->input('product_id');
        $quantity = $request->input('quantity');

        $stock = Stock::where('product_id', $product_id)->first();

        if ($stock) {
            $stock->quantity += $quantity;
            $stock->updated_by = auth()->user()->id;
            $stock->save();
        } else {
            $newStock = new Stock();
            $newStock->product_id = $product_id;
            $newStock->quantity = $quantity;
            $newStock->created_by = auth()->user()->id;
            $newStock->save();
        }

        $orderReturn = OrderReturn::where('product_id', $product_id)
            ->where('order_id', $request->order_id)
            ->first();

        if ($orderReturn) {
            $orderReturn->new_quantity -= $quantity;
            $orderReturn->return_stock = $quantity;
            $orderReturn->save();
        }


        return redirect()->back()->with('success', 'Stock updated successfully.');
    }

    public function sendToSystemLose(Request $request)
    {
        // dd($request->all());
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|numeric|min:1',
        ]);

        $product_id = $request->input('product_id');
        $quantity = $request->input('quantity');


        $systemLoss = new SystemLose();
        $systemLoss->product_id = $product_id;
        $systemLoss->order_id = $request->order_id;
        $systemLoss->quantity = $quantity;
        $systemLoss->reason = $request->input('reason');
        $systemLoss->created_by = auth()->user()->id;
        $systemLoss->save();


        $orderReturn = OrderReturn::where('product_id', $product_id)
            ->where('order_id', $request->order_id)
            ->first();
            

        if ($orderReturn) {
            $orderReturn->new_quantity -= $quantity;
            $orderReturn->system_lose = $quantity;
            $orderReturn->save();
        }

        return redirect()->back()->with('success', 'Sent to system lose successfully.');
    }

    public function updateStatus(Request $request)
    {
        $request->validate([
            'purchase_id' => 'required|integer|exists:purchases,id',
            'status' => 'required|integer|in:1,2,3,4'
        ]);

        $purchase = Purchase::find($request->purchase_id);
        $purchase->status = $request->status;
        $purchase->save();

        return response()->json(['success' => true]);
    }

    public function missingProduct($id)
    {
        $purchase = Purchase::with('purchaseHistory.product')->findOrFail($id);
        $warehouses = Warehouse::orderby('id','DESC')->where('status', 1)->get();
        $purchaseCount = PurchaseHistory::where('purchase_id', $id)->count();
        return view('admin.stock.missing_product', compact('purchase', 'warehouses', 'purchaseCount'));
    }

    public function missingPurchaseProduct(Request $request, $purchaseId)
    {
        $request->validate([
            'quantities.*' => 'required|array',
        ]);
    
        foreach ($request->quantities as $historyId => $quantities) {
            foreach ($quantities as $index => $quantity) {
    
                $purchaseHistory = PurchaseHistory::find($historyId);
    
                if (!$purchaseHistory) {
                    continue;
                }
    
                $purchaseHistory->remaining_product_quantity -= $quantity;
                $purchaseHistory->missing_product_quantity += $quantity;
                $purchaseHistory->save();

                $size = $request->sizes[$historyId][0];
                $color = $request->colors[$historyId][0];

                $missing = new SystemLose();
                $missing->product_id = $purchaseHistory->product_id;
                $missing->purchase_id = $purchaseHistory->purchase_id;
                $missing->quantity = $quantity;
                $missing->reason = "Product Missing";
                $missing->save();
    
                
            }
        }
    
        return redirect()->back()->with('success', 'Missing product recorded successfully.');
    }

    public function createOrder()
    {
        if (!(in_array('7', json_decode(auth()->user()->role->permission)))) {
            return redirect()->back()->with('error', 'Sorry, You do not have permission to access that page.');
        }

        $motherVassels = MotherVassel::select('id', 'name','code')->orderby('id','DESC')->get();
        $suppliers = Supplier::orderby('id','DESC')->where('status', 1)->get();
        $expenses = ChartOfAccount::where('account_head', 'Expenses')->get();
        
        return view('admin.stock.create_order', compact('motherVassels', 'suppliers', 'expenses'));
    }

    public function storeOrder(Request $request)
    {
        $request->validate([
            'consignment_number' => 'required|string|max:255',
            'mother_vassels_id' => 'required',
            'supplier_id' => 'required',
            'advance_date' => 'required',
            'purchase_type' => 'required|string|max:50',
            'advance_amount' => 'nullable|numeric|min:0|required_if:purchase_type,Due',
            'advance_quantity' => 'nullable|integer|min:1|required_if:purchase_type,Due',
            'expenses' => 'nullable|array',
            'expenses.*.expense_id' => 'required|numeric',
            'expenses.*.payment_type' => 'nullable|string',
            'expenses.*.amount' => 'required|numeric',
            'expenses.*.description' => 'nullable|string',
            'expenses.*.note' => 'nullable|string',
        ]);

        DB::beginTransaction();

        try {
            $purchase = new Purchase();
            $purchase->consignment_number = $request->consignment_number;
            $purchase->advance_date = $request->advance_date;
            $purchase->supplier_id = $request->supplier_id;
            $purchase->mother_vassels_id = $request->mother_vassels_id;
            $purchase->purchase_type = $request->purchase_type;
            $purchase->advance_amount = $request->advance_amount;
            $purchase->advance_quantity = $request->advance_quantity;
            $purchase->cost_per_unit = $request->cost_per_unit;
            $purchase->created_by = auth()->user()->id;
            $purchase->save();

            $transaction = new Transaction();
            $transaction->date = $request->advance_date;
            $transaction->purchase_id = $purchase->id;
            $transaction->table_type = "Purchase";
            $transaction->supplier_id = $request->supplier_id;
            if($request->purchase_type == "Due"){
                $transaction->transaction_type = "Due";
            } else {
                $transaction->transaction_type = "Advance";
            }

            $transaction->payment_type = $request->purchase_type;
            $transaction->amount = $request->advance_amount;
            $transaction->at_amount = $request->advance_amount;
            $transaction->created_by = auth()->id();
            $transaction->save();

            $transaction->tran_id = 'PR' . date('ymd') . str_pad($transaction->id, 4, '0', STR_PAD_LEFT);
            $transaction->save();

            $expenses = $request->input('expenses', []);

            foreach ($expenses as $expense) {
                $transactionExpense = new Transaction();
                $transactionExpense->date = $request->advance_date;
                $transactionExpense->table_type = 'Expenses';
                $transactionExpense->purchase_id = $purchase->id;
                $transactionExpense->supplier_id = $request->supplier_id;
                $transactionExpense->amount = $expense['amount'];
                $transactionExpense->at_amount = $expense['amount'];
                $transactionExpense->payment_type = $expense['payment_type'];
                $transactionExpense->chart_of_account_id = $expense['expense_id'];
                $transactionExpense->expense_id = $expense['expense_id'];
                $transactionExpense->description = $expense['description'];
                $transactionExpense->note = $expense['note'];
                $transactionExpense->transaction_type = 'Current';
                $transactionExpense->created_by = auth()->id();
                $transactionExpense->save();

                $transactionExpense->tran_id = 'EX' . date('ymd') . str_pad($transactionExpense->id, 4, '0', STR_PAD_LEFT);
                $transactionExpense->save();
            }

            DB::commit();

            return response()->json(['message' => 'Order created successfully!'], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Something went wrong!', 'error' => $e->getMessage()], 500);
        }
    }

    public function editOrder($id)
    {
        if (!(in_array('7', json_decode(auth()->user()->role->permission)))) {
            return redirect()->back()->with('error', 'Sorry, You do not have permission to access that page.');
        }

        $purchase = Purchase::findOrFail($id);
        $motherVassels = MotherVassel::select('id', 'name','code')->orderby('id','DESC')->get();
        $suppliers = Supplier::orderby('id','DESC')->where('status', 1)->get();
        $expenses = ChartOfAccount::where('account_head', 'Expenses')->get();
        $purchaseExpenses = Transaction::where('purchase_id', $id)
            ->where('table_type', 'Expenses')
            ->get();
        
        return view('admin.stock.edit_order', compact('purchase', 'motherVassels', 'suppliers', 'expenses', 'purchaseExpenses'));
    }

    public function updateOrder(Request $request, $id)
    {
        $request->validate([
            'consignment_number' => 'required|string|max:255',
            'mother_vassels_id' => 'required',
            'supplier_id' => 'required',
            'advance_date' => 'required',
            'purchase_type' => 'required|string|max:50',
            'advance_amount' => 'nullable|numeric|min:0',
            'advance_quantity' => 'nullable|integer|min:1',
            'expenses' => 'nullable|array',
            'expenses.*.expense_id' => 'required|numeric',
            'expenses.*.payment_type' => 'nullable|string',
            'expenses.*.amount' => 'required|numeric',
            'expenses.*.description' => 'nullable|string',
            'expenses.*.note' => 'nullable|string',
        ]);

        DB::beginTransaction();

        try {
            $purchase = Purchase::findOrFail($id);
            
            $purchase->update([
                'consignment_number' => $request->consignment_number,
                'advance_date' => $request->advance_date,
                'supplier_id' => $request->supplier_id,
                'mother_vassels_id' => $request->mother_vassels_id,
                'purchase_type' => $request->purchase_type,
                'advance_amount' => $request->advance_amount,
                'advance_quantity' => $request->advance_quantity,
                'cost_per_unit' => $request->cost_per_unit,
            ]);

            $mainTransaction = Transaction::where('purchase_id', $id)
                ->where('table_type', 'Purchase')
                ->first();

            if ($mainTransaction) {
                $mainTransaction->update([
                    'date' => $request->advance_date,
                    'amount' => $request->advance_amount,
                    'at_amount' => $request->advance_amount,
                    'payment_type' => $request->purchase_type,
                ]);
            }

            $newExpenses = $request->input('expenses', []);
            $newExpenseIds = collect($newExpenses)->pluck('expense_id')->toArray();

            Transaction::where('purchase_id', $id)
                ->where('table_type', 'Expenses')
                ->whereNotIn('chart_of_account_id', $newExpenseIds)
                ->delete();

            foreach ($newExpenses as $expense) {
                $transactionExpense = Transaction::where('purchase_id', $id)
                    ->where('chart_of_account_id', $expense['expense_id'])
                    ->where('table_type', 'Expenses')
                    ->first();

                if ($transactionExpense) {
                    $transactionExpense->update([
                        'date' => $request->advance_date,
                        'amount' => $expense['amount'],
                        'at_amount' => $expense['amount'],
                        'payment_type' => $expense['payment_type'],
                        'description' => $expense['description'],
                        'note' => $expense['note'],
                    ]);
                } else {
                    $newTransaction = new Transaction();
                    $newTransaction->date = $request->advance_date;
                    $newTransaction->table_type = 'Expenses';
                    $newTransaction->purchase_id = $purchase->id;
                    $newTransaction->supplier_id = $request->supplier_id;
                    $newTransaction->amount = $expense['amount'];
                    $newTransaction->at_amount = $expense['amount'];
                    $newTransaction->payment_type = $expense['payment_type'];
                    $newTransaction->chart_of_account_id = $expense['expense_id'];
                    $newTransaction->expense_id = $expense['expense_id'];
                    $newTransaction->description = $expense['description'];
                    $newTransaction->note = $expense['note'];
                    $newTransaction->transaction_type = 'Current';
                    $newTransaction->created_by = auth()->id();
                    $newTransaction->save();

                    $newTransaction->tran_id = 'EX' . date('ymd') . str_pad($newTransaction->id, 4, '0', STR_PAD_LEFT);
                    $newTransaction->save();
                }
            }

            DB::commit();

            return response()->json(['message' => 'Order updated successfully!'], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Something went wrong!', 'error' => $e->getMessage()], 500);
        }
    }

    public function advancePayment(Request $request)
    {
        $request->validate([
            'order_id' => 'required',
            'paymentAmount' => 'required',
            'payment_type' => 'required',
            'paymentNote' => 'nullable',
        ]);

        $transaction = new Transaction();
        $transaction->table_type = "Purchase";
        $transaction->purchase_id = $request->order_id;

        if ($request->hasFile('document')) {
            $uploadedFile = $request->file('document');
            $randomName = mt_rand(10000000, 99999999).'.'.$uploadedFile->getClientOriginalExtension();
            $destinationPath = 'images/transaction/';
            $uploadedFile->move(public_path($destinationPath), $randomName);
            $transaction->document = '/' . $destinationPath . $randomName;
        }

        $transaction->amount = $request->paymentAmount;
        $transaction->at_amount = $request->paymentAmount;
        if($request->payment_type == "Due"){
          $transaction->transaction_type = "Due";
        } else {
          $transaction->transaction_type = "Advance";
        }
        $transaction->payment_type = $request->payment_type;
        $transaction->note = $request->paymentNote;
        $transaction->date = date('Y-m-d');
        $transaction->save();

        $transaction->tran_id = 'PR' . date('ymd') . str_pad($transaction->id, 4, '0', STR_PAD_LEFT);
        $transaction->save();

        $purchase = Purchase::find($request->order_id);
        $purchase->advance_amount += $request->paymentAmount;
        $purchase->save();

          return response()->json([
            'status' => 'success',
            'message' => 'Payment processed successfully!',
        ]);
    }

    public function orderList()
    {
        if (!(in_array('8', json_decode(auth()->user()->role->permission)))) {
          return redirect()->back()->with('error', 'Sorry, You do not have permission to access that page.');
        }

        $data = Purchase::latest()->get();
        return view('admin.stock.order_list', compact('data'));
    }

    public function advanceTransactions($id)
    {
        $transactions = Transaction::where('purchase_id', $id)->where('table_type', 'Purchase')->select('id', 'date', 'note', 'payment_type', 'table_type', 'at_amount', 'document', 'tran_id')->latest()->get();

        $totalDrAmount = 0;

        $totalCrAmount = $transactions->sum('at_amount');

        $totalBalance = $totalDrAmount - $totalCrAmount;

        return view('admin.stock.advance_transactions', compact('transactions','totalBalance'));
    }

    public function advancePaymentUpdate(Request $request)
    {
        $request->validate([
            'transactionId' => 'required|integer|exists:transactions,id',
            'at_amount' => 'required|numeric|min:0',
            'note' => 'nullable|string',
            'document' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048'
        ]);
    
        $transaction = Transaction::findOrFail($request->transactionId);
    
        $transaction->at_amount = $request->at_amount;
        $transaction->amount = $request->amount;
        $transaction->note = $request->note;
    
        if ($request->hasFile('document')) {
            if ($transaction->document && file_exists(public_path($transaction->document))) {
                unlink(public_path($transaction->document));
            }
  
            $uploadedFile = $request->file('document');
            $randomName = mt_rand(10000000, 99999999).'.'.$uploadedFile->getClientOriginalExtension();
            $destinationPath = 'images/transaction/';
            $uploadedFile->move(public_path($destinationPath), $randomName);
            $transaction->document = '/' . $destinationPath . $randomName;

        }

        $transaction->updated_by = auth()->user()->id;

        $transaction->save();

        if ($transaction->purchase_id) {
          $totalAdvance = Transaction::where('purchase_id', $transaction->purchase_id)->sum('at_amount');

          $transaction->purchase->update([
              'advance_amount' => $totalAdvance
          ]);
        }
    
        return response()->json([
            'success' => true,
            'message' => 'Transaction updated successfully!',
        ]);
    }

}
