<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Product;
use App\Models\Order;
use App\Models\OrderDetails;
use App\Models\Stock;
use App\Models\CompanyDetails;
use PDF;
use App\Models\Color;
use App\Models\Size;
use App\Models\StockHistory;
use App\Models\Warehouse;
use App\Models\Transaction;
use Illuminate\Support\Facades\Mail;
use App\Mail\OrderConfirmation;
use App\Models\ContactEmail;

class InHouseSellController extends Controller
{
    public function inHouseSell()
    {
        $products = Product::orderby('id', 'DESC')->select('id', 'name', 'price', 'product_code')->get();
        $colors = Color::where('status', 1)->select('id', 'color')->orderby('id', 'DESC')->get();
        $sizes = Size::where('status', 1)->select('id', 'size')->orderby('id', 'DESC')->get();
        $warehouses = Warehouse::select('id', 'name', 'location')->where('status', 1)->get();
        $customers = User::where('is_type', '0')->where('status', 1)->orderby('id', 'DESC')->get();
        return view('admin.in_house_sell.create', compact('customers', 'products', 'colors', 'sizes', 'warehouses'));
    }

    public function inHouseSellStore(Request $request)
    {
        $validated = $request->validate([
            'purchase_date' => 'required|date',
            'user_id' => 'required|exists:users,id',
            'payment_method' => 'required|string',
            'ref' => 'nullable|string',
            'remarks' => 'nullable|string',
            'discount' => 'nullable',
            'products' => 'required|json',
        ], [
            'user_id.required' => 'Please choose a wholesaler.',
            'user_id.exists' => 'Please choose a valid wholesaler.',
        ]);

        $products = json_decode($validated['products'], true);

        $itemTotalAmount = array_reduce($products, function ($carry, $product) {
            return $carry + $product['total_price'];
        }, 0);

        $vatPercent = $request->vat_percent;

        $netAmount = $itemTotalAmount - $validated['discount'] + $request->vat;

        $order = new Order();
        $order->invoice = random_int(100000, 999999);
        $order->warehouse_id = $request->warehouse_id;
        $order->purchase_date = $validated['purchase_date'];
        $order->user_id = $validated['user_id'];
        $order->payment_method = $validated['payment_method'];
        $order->ref = $validated['ref'];
        $order->remarks = $validated['remarks'];
        $order->discount_amount = $validated['discount'];
        $order->net_amount = $netAmount;
        $order->vat_amount = $request->vat;
        $order->vat_percent = $request->vat_percent;
        $order->paid_amount = $request->cash_payment + $request->bank_payment;
        $order->due_amount = $netAmount - $request->cash_payment - $request->bank_payment;
        $order->subtotal_amount = $itemTotalAmount;
        $order->order_type = 1;
        $order->status = 2;
        $order->save();

        $transaction = new Transaction();
        $transaction->date = $validated['purchase_date'];
        $transaction->customer_id = $validated['user_id'];
        $transaction->order_id = $order->id;
        $transaction->table_type = "Sales";
        $transaction->ref = $validated['ref'];
        $transaction->payment_type = "Credit";
        $transaction->transaction_type = "Current";
        $transaction->amount = $itemTotalAmount;
        $transaction->vat_amount = $request->vat;
        $transaction->discount = $validated['discount'] ?? 0.00;
        $transaction->at_amount = $netAmount;
        $transaction->save();
        $transaction->tran_id = 'SL' . date('Ymd') . str_pad($transaction->id, 4, '0', STR_PAD_LEFT);
        $transaction->save();

        if ($request->cash_payment) {
            $cashtransaction = new Transaction();
            $cashtransaction->date = $validated['purchase_date'];
            $cashtransaction->customer_id = $validated['user_id'];
            $cashtransaction->order_id = $order->id;
            $cashtransaction->table_type = "Sales";
            $cashtransaction->ref = $validated['ref'];
            $cashtransaction->payment_type = "Cash";
            $cashtransaction->transaction_type = "Received";
            $cashtransaction->amount = $request->cash_payment;
            $cashtransaction->at_amount = $request->cash_payment;
            $cashtransaction->save();
            $cashtransaction->tran_id = 'SL' . date('Ymd') . str_pad($cashtransaction->id, 4, '0', STR_PAD_LEFT);
            $cashtransaction->save();
        }

        if ($request->bank_payment) {
            $banktransaction = new Transaction();
            $banktransaction->date = $validated['purchase_date'];
            $banktransaction->customer_id = $validated['user_id'];
            $banktransaction->order_id = $order->id;
            $banktransaction->table_type = "Sales";
            $banktransaction->ref = $validated['ref'];
            $banktransaction->payment_type = "Bank";
            $banktransaction->transaction_type = "Received";
            $banktransaction->amount = $request->bank_payment;
            $banktransaction->at_amount = $request->bank_payment;
            $banktransaction->save();
            $banktransaction->tran_id = 'SL' . date('Ymd') . str_pad($banktransaction->id, 4, '0', STR_PAD_LEFT);
            $banktransaction->save();
        }

        foreach ($products as $product) {

            $unitPrice = $product['unit_price'];
            $quantity = $product['quantity'];

            $vatAmount = ($unitPrice * $vatPercent) / 100;

            $totalVat = $vatAmount * $quantity;

            $totalPriceWithVat = ($unitPrice + $vatAmount) * $quantity;

            $orderDetail = new OrderDetails();
            $orderDetail->order_id = $order->id;
            $orderDetail->warehouse_id = $request->warehouse_id;
            $orderDetail->product_id = $product['product_id'];
            $orderDetail->stock_history_id = $product['stock_history_id'];
            $orderDetail->quantity = $quantity;
            $orderDetail->price_per_unit = $unitPrice;
            $orderDetail->total_price = $product['total_price'];
            $orderDetail->vat_percent = $vatPercent;
            $orderDetail->total_vat = $totalVat;
            $orderDetail->total_price_with_vat = $totalPriceWithVat;
            $orderDetail->status = 1;
            $orderDetail->save();

            $requiredQty = $product['quantity'];

            if ($request->warehouse_id) {
                $stock = Stock::where('product_id', $product['product_id'])
                    ->where('warehouse_id', $request->warehouse_id)
                    ->first();

                if ($stock) {
                    $stock->quantity -= $product['quantity'];
                    $stock->save();
                } else {
                    $stock = new Stock();
                    $stock->warehouse_id = $request->warehouse_id;
                    $stock->product_id = $product['product_id'];
                    $stock->quantity = -$product['quantity'];
                    $stock->created_by = auth()->user()->id;
                    $stock->save();
                }

                $stockHistory = StockHistory::find($product['stock_history_id']);

                if ($stockHistory && $stockHistory->available_qty > 0) {
                    $requiredQty = $product['quantity'];

                    if ($requiredQty > 0) {
                        if ($stockHistory->available_qty >= $requiredQty) {
                            $stockHistory->available_qty -= $requiredQty;
                            $stockHistory->selling_qty += $requiredQty;
                            $stockHistory->save();
                            $requiredQty = 0;
                        } else {
                            $requiredQty -= $stockHistory->available_qty;
                            $stockHistory->available_qty = 0;
                            $stockHistory->selling_qty += $requiredQty;
                            $stockHistory->save();
                        }
                    }
                }
            }
        }

        $encoded_order_id = base64_encode($order->id);
        $pdfUrl = route('in-house-sell.generate-pdf', ['encoded_order_id' => $encoded_order_id]);

        // Mail::to($order->user->email)->send(new OrderConfirmation($order, $pdfUrl));

        // $contactEmails = ContactEmail::where('status', 1)->pluck('email');

        // foreach ($contactEmails as $email) {
        //     Mail::to($email)->send(new OrderConfirmation($order, $pdfUrl));
        // }

        return response()->json([
            'pdf_url' => $pdfUrl,
            'message' => 'Order placed successfully'
        ], 200);

        return response()->json(['message' => 'Order created successfully', 'order_id' => $order->id], 201);
    }

    public function inHouseQuotationSellStore(Request $request)
    {
        $data = Order::find($request->order_id);
        $data->order_type = 1;
        $data->save();

        return back()->with('success', 'Order create successfully!');
    }

    public function generatePDF($encoded_order_id)
    {
        $order_id = base64_decode($encoded_order_id);
        $order = Order::with(['orderDetails', 'user'])->findOrFail($order_id);

        $data = [
            'order' => $order,
            'currency' => CompanyDetails::value('currency'),
        ];

        $pdf = PDF::loadView('admin.in_house_sell.in_house_sell_order_pdf', $data);

        return $pdf->stream('order_' . $order->id . '.pdf');
    }

    public function makeQuotationStore(Request $request)
    {
        $validated = $request->validate([
            'purchase_date' => 'required|date',
            'user_id' => 'required|exists:users,id',
            'payment_method' => 'required|string',
            'ref' => 'nullable|string',
            'remarks' => 'nullable|string',
            'discount' => 'nullable',
            'products' => 'required|json',
        ]);

        $products = json_decode($validated['products'], true);

        $itemTotalAmount = array_reduce($products, function ($carry, $product) {
            return $carry + $product['total_price'];
        }, 0);

        $vatPercent = $request->vat_percent;
        $netAmount = $itemTotalAmount - $validated['discount'] + $request->vat;

        $order = new Order();
        $order->invoice = random_int(100000, 999999);
        $order->purchase_date = $validated['purchase_date'];
        $order->user_id = $validated['user_id'];
        $order->payment_method = $validated['payment_method'];
        $order->ref = $validated['ref'];
        $order->remarks = $validated['remarks'];
        $order->discount_amount = $validated['discount'];
        $order->net_amount = $netAmount;
        $order->vat_amount = $request->vat;
        $order->vat_percent = $request->vat_percent;
        $order->subtotal_amount = $itemTotalAmount;
        $order->order_type = 2;
        $order->status = 1;
        $order->save();

        foreach ($products as $product) {
            $unitPrice = $product['unit_price'];
            $quantity = $product['quantity'];

            $vatAmount = ($unitPrice * $vatPercent) / 100;

            $totalVat = $vatAmount * $quantity;

            $totalPriceWithVat = ($unitPrice + $vatAmount) * $quantity;
            $orderDetail = new OrderDetails();
            $orderDetail->order_id = $order->id;
            $orderDetail->warehouse_id = $request->warehouse_id;
            $orderDetail->product_id = $product['product_id'];
            $orderDetail->quantity = $quantity;
            $orderDetail->price_per_unit = $unitPrice;
            $orderDetail->total_price = $product['total_price'];
            $orderDetail->vat_percent = $vatPercent;
            $orderDetail->total_vat = $totalVat;
            $orderDetail->total_price_with_vat = $totalPriceWithVat;
            $orderDetail->status = 1;
            $orderDetail->save();
        }

        $encoded_order_id = base64_encode($order->id);
        $pdfUrl = route('in-house-sell.generate-pdf', ['encoded_order_id' => $encoded_order_id]);

        Mail::to($order->user->email)->send(new OrderConfirmation($order, $pdfUrl));

        $contactEmails = ContactEmail::where('status', 1)->pluck('email');

        foreach ($contactEmails as $email) {
            Mail::to($email)->send(new OrderConfirmation($order, $pdfUrl));
        }

        return response()->json(['message' => 'Quotation created successfully', 'order_id' => $order->id], 201);
    }

    public function allquotations()
    {
        $inHouseOrders = Order::with('user')
            ->where('order_type', 2)
            ->orderBy('id', 'desc')
            ->get();

        return view('admin.in_house_sell.quotations', compact('inHouseOrders'));
    }

    public function checkStock(Request $request)
    {
        $warehouseId = $request->input('warehouse_id');
        $productId = $request->input('product_id');
        $size = $request->input('size');
        $color = $request->input('color');

        if (empty($warehouseId) || empty($productId)) {
            return response()->json(['error' => 'Warehouse ID and Product ID are required'], 400);
        }

        $stock = Stock::where('warehouse_id', $warehouseId)
            ->where('product_id', $productId)
            ->where(function ($query) use ($size, $color) {
                if ($size) {
                    $query->where('size', $size);
                }
                if ($color) {
                    $query->where('color', $color);
                }
            })
            ->first();

        if (!$stock) {
            return response()->json(['in_stock' => false]);
        }

        return response()->json(['in_stock' => $stock->quantity > 0]);
    }

    public function editOrder($orderId)
    {
        $order = Order::with(['user', 'orderDetails', 'transactions'])->findOrFail($orderId);
        $cashAmount = $order->transactions->where('payment_type', 'Cash')->first();
        $bankAmount = $order->transactions->where('payment_type', 'Bank')->first();
        $discountAmount = $order->transactions->where('transaction_type', 'Current')->where('discount', '>', 0)->first();

        $customers = User::where('is_type', '0')->where('status', 1)->orderby('id', 'asc')->get();
        $products = Product::orderby('id', 'DESC')->get();
        $colors = Color::orderby('id', 'DESC')->where('status', 1)->get();
        $sizes = Size::orderby('id', 'DESC')->where('status', 1)->get();
        $warehouses = Warehouse::select('id', 'name', 'location')->where('status', 1)->get();
        return view('admin.in_house_sell.edit_order', compact('customers', 'products', 'colors', 'sizes', 'warehouses', 'order', 'cashAmount', 'bankAmount', 'discountAmount'));
    }

    public function updateOrder(Request $request)
    {
        $order = Order::findOrFail($request->id);

        $userIdRule = $request->user_id ? 'required|exists:users,id' : 'nullable';

        $validated = $request->validate([
            'id' => 'required|exists:orders,id',
            'purchase_date' => 'required|date',
            'warehouse_id' => 'required|exists:warehouses,id',
            'payment_method' => 'required|string',
            'ref' => 'nullable|string',
            'remarks' => 'nullable|string',
            'discount' => 'nullable|numeric',
            'products' => 'required|json',

            'user_id' => $userIdRule,
        ], [
            'user_id.required' => 'Please choose a wholesaler.',
            'user_id.exists' => 'Please choose a valid wholesaler.',
        ]);

        $order = Order::findOrFail($validated['id']);

        $products = json_decode($validated['products'], true);

        $itemTotalAmount = array_reduce($products, function ($carry, $product) {
            return $carry + $product['total_price'];
        }, 0);

        $netAmount = $itemTotalAmount - $validated['discount'] + $request->vat;

        $order->purchase_date = $validated['purchase_date'];
        $order->user_id = $validated['user_id'];
        $order->payment_method = $validated['payment_method'];
        $order->ref = $validated['ref'];
        $order->remarks = $validated['remarks'];
        $order->discount_amount = $validated['discount'];
        $order->net_amount = $netAmount;
        $order->vat_amount = $request->vat;
        $order->vat_percent = $request->vat_percent;
        $order->paid_amount = $request->cash_payment + $request->bank_payment;
        $order->due_amount = $netAmount - $request->cash_payment - $request->bank_payment;
        $order->subtotal_amount = $itemTotalAmount;
        if ($order->order_type != 0) {
            $order->order_type = 1;
            $order->save();
        }
        $order->save();

        $transaction = Transaction::where('order_id', $order->id)->where('transaction_type', 'Current')->where('payment_type', 'Credit')->first();
        if ($transaction) {
            $transaction->date = $validated['purchase_date'];
            $transaction->customer_id = $validated['user_id'];
            $transaction->amount = $itemTotalAmount;
            $transaction->vat_amount = $request->vat;
            $transaction->discount = $validated['discount'] ?? 0.00;
            $transaction->at_amount = $netAmount;
            $transaction->save();
        }

        if ($request->cash_payment) {
            $cashtransaction = Transaction::where('order_id', $order->id)->where('payment_type', 'Cash')->first();
            if ($cashtransaction) {
                $cashtransaction->amount = $request->cash_payment;
                $cashtransaction->at_amount = $request->cash_payment;
                $cashtransaction->save();
            }
        }

        if ($request->bank_payment) {
            $banktransaction = Transaction::where('order_id', $order->id)->where('payment_type', 'Bank')->first();
            if ($banktransaction) {
                $banktransaction->amount = $request->bank_payment;
                $banktransaction->at_amount = $request->bank_payment;
                $banktransaction->save();
            }
        }

        $existingOrderDetails = OrderDetails::where('order_id', $order->id)->get();

        OrderDetails::where('order_id', $order->id)->delete();

        foreach ($products as $product) {
            $unitPrice = $product['unit_price'];
            $quantity = $product['quantity'];
            $vatPercent = $validated['vat_percent'] ?? 0;

            $vatAmount = ($unitPrice * $vatPercent) / 100;
            $totalVat = $vatAmount * $quantity;
            $totalPriceWithVat = ($unitPrice + $vatAmount) * $quantity;

            $orderDetail = new OrderDetails();
            $orderDetail->order_id = $order->id;
            $orderDetail->warehouse_id = $validated['warehouse_id'];
            $orderDetail->product_id = $product['product_id'];
            $orderDetail->stock_history_id = $product['stock_history_id'];
            $orderDetail->quantity = $quantity;
            $orderDetail->price_per_unit = $unitPrice;
            $orderDetail->total_price = $product['total_price'];
            $orderDetail->vat_percent = $vatPercent;
            $orderDetail->total_vat = $totalVat;
            $orderDetail->total_price_with_vat = $totalPriceWithVat;
            $orderDetail->status = 1;
            $orderDetail->save();

            if ($validated['warehouse_id']) {
                $stock = Stock::where('product_id', $product['product_id'])
                    ->where('warehouse_id', $validated['warehouse_id'])
                    ->first();

                $oldQuantity = $existingOrderDetails->where('product_id', $product['product_id'])->first()->quantity ?? 0;

                $quantityDifference = $quantity - $oldQuantity;

                if ($stock) {
                    $stock->quantity -= $quantityDifference;
                    $stock->save();
                } else {
                    $stock = new Stock();
                    $stock->warehouse_id = $validated['warehouse_id'];
                    $stock->product_id = $product['product_id'];
                    $stock->quantity = -$quantity;
                    $stock->created_by = auth()->user()->id;
                    $stock->save();
                }

                $stockHistory = StockHistory::find($product['stock_history_id']);
                if ($stockHistory) {
                    $stockHistory->available_qty -= $quantityDifference;

                    $stockHistory->selling_qty += $quantityDifference;
                    $stockHistory->save();
                }
            }
        }

        $encoded_order_id = base64_encode($order->id);
        $pdfUrl = route('in-house-sell.generate-pdf', ['encoded_order_id' => $encoded_order_id]);

        return response()->json([
            'pdf_url' => $pdfUrl,
            'message' => 'Order updated successfully'
        ], 200);
    }

    // public function updateOrder(Request $request)
    // {
    //     $order = Order::findOrFail($request->id);

    //     $userIdRule = $request->user_id ? 'required|exists:users,id' : 'nullable';

    //     $validated = $request->validate([
    //         'id' => 'required|exists:orders,id',
    //         'purchase_date' => 'required|date',
    //         'warehouse_id' => 'required|exists:warehouses,id',
    //         'payment_method' => 'required|string',
    //         'ref' => 'nullable|string',
    //         'remarks' => 'nullable|string',
    //         'discount' => 'nullable|numeric',
    //         'products' => 'required|json',
    //         'user_id' => $userIdRule,
    //     ], [
    //         'user_id.required' => 'Please choose a wholesaler.',
    //         'user_id.exists' => 'Please choose a valid wholesaler.',
    //     ]);

    //     $products = json_decode($validated['products'], true);
    //     $itemTotalAmount = array_reduce($products, function ($carry, $product) {
    //         return $carry + $product['total_price'];
    //     }, 0);

    //     $netAmount = $itemTotalAmount - $validated['discount'] + $request->vat;

    //     $order->purchase_date = $validated['purchase_date'];
    //     $order->user_id = $validated['user_id'];
    //     $order->payment_method = $validated['payment_method'];
    //     $order->ref = $validated['ref'];
    //     $order->remarks = $validated['remarks'];
    //     $order->discount_amount = $validated['discount'];
    //     $order->net_amount = $netAmount;
    //     $order->vat_amount = $request->vat;
    //     $order->vat_percent = $request->vat_percent;
    //     $order->paid_amount = $request->cash_payment + $request->bank_payment;
    //     $order->due_amount = $netAmount - $request->cash_payment - $request->bank_payment;
    //     $order->subtotal_amount = $itemTotalAmount;
    //     $order->save();

    //     $transaction = Transaction::where('order_id', $order->id)->where('transaction_type', 'Current')->where('payment_type', 'Credit')->first();
    //     if ($transaction) {
    //         $transaction->date = $validated['purchase_date'];
    //         $transaction->customer_id = $validated['user_id'];
    //         $transaction->amount = $itemTotalAmount;
    //         $transaction->vat_amount = $request->vat;
    //         $transaction->discount = $validated['discount'] ?? 0.00;
    //         $transaction->at_amount = $netAmount;
    //         $transaction->save();
    //     }

    //     if ($request->cash_payment) {
    //         $cashtransaction = Transaction::where('order_id', $order->id)->where('payment_type', 'Cash')->first();
    //         if ($cashtransaction) {
    //             $cashtransaction->amount = $request->cash_payment;
    //             $cashtransaction->at_amount = $request->cash_payment;
    //             $cashtransaction->save();
    //         }
    //     }

    //     if ($request->bank_payment) {
    //         $banktransaction = Transaction::where('order_id', $order->id)->where('payment_type', 'Bank')->first();
    //         if ($banktransaction) {
    //             $banktransaction->amount = $request->bank_payment;
    //             $banktransaction->at_amount = $request->bank_payment;
    //             $banktransaction->save();
    //         }
    //     }

    //     foreach ($products as $product) {
    //         $unitPrice = $product['unit_price'];
    //         $quantity = $product['quantity'];
    //         $productId = $product['product_id'];
    //         $vatAmount = ($unitPrice * $request->vat_percent) / 100;
    //         $totalVat = $vatAmount * $quantity;
    //         $totalPriceWithVat = ($unitPrice + $vatAmount) * $quantity;
        
    //         $originalDetail = OrderDetails::where('order_id', $order->id)
    //             ->where('product_id', $productId)
    //             ->first();
        
    //         $orderDetail = OrderDetails::updateOrCreate(
    //             ['order_id' => $order->id, 'product_id' => $productId],
    //             [
    //                 'warehouse_id' => $request->warehouse_id,
    //                 'quantity' => $quantity,
    //                 'price_per_unit' => $unitPrice,
    //                 'total_price' => $product['total_price'],
    //                 'vat_percent' => $request->vat_percent,
    //                 'total_vat' => $totalVat,
    //                 'total_price_with_vat' => $totalPriceWithVat,
    //                 'status' => 1
    //             ]
    //         );
        
    //         // Calculate the quantity difference
    //         $quantityDifference = $quantity - ($originalDetail ? $originalDetail->quantity : 0);
        
    //         // Update stock
    //         $stock = Stock::where('product_id', $productId)
    //             ->where('warehouse_id', $request->warehouse_id)
    //             ->first();
        
    //         if ($stock) {
    //             $stock->quantity -= $quantityDifference; // Adjust stock based on the difference
    //             $stock->save();
    //         } else {
    //             // Create a new stock record if it doesn't exist
    //             $stock = new Stock();
    //             $stock->warehouse_id = $request->warehouse_id;
    //             $stock->product_id = $productId;
    //             $stock->quantity = -$quantityDifference; // Set negative quantity for new stock
    //             $stock->created_by = auth()->user()->id;
    //             $stock->save();
    //         }
        
    //         // Update stock history
    //         $stockHistory = StockHistory::find($product['stock_history_id']);
    //         if ($stockHistory) {
    //             $stockHistory->available_qty -= $quantityDifference; // Adjust available quantity
    //             $stockHistory->selling_qty += $quantityDifference; // Update selling quantity
    //             $stockHistory->save();
    //         }
    //     }

    //     $encoded_order_id = base64_encode($order->id);
    //     $pdfUrl = route('in-house-sell.generate-pdf', ['encoded_order_id' => $encoded_order_id]);

    //     return response()->json([
    //         'pdf_url' => $pdfUrl,
    //         'message' => 'Order updated successfully'
    //     ], 200);
    // }

    public function fetchStockHistory(Request $request)
    {
        $request->validate([
            'warehouse_id' => 'required|integer',
            'product_id'   => 'required|integer',
        ]);

        $stockHistories = StockHistory::where('warehouse_id', $request->warehouse_id)
            ->where('product_id', $request->product_id)
            ->where('available_qty', '>', 0)
            ->orderBy('id', 'asc')
            ->get(['id', 'selling_price', 'available_qty']);

        return response()->json([
            'success' => true,
            'stockHistories' => $stockHistories,
        ]);
    }
}
