<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use App\Models\Purchase;
use App\Models\PurchaseHistory;
use App\Models\Stock;
use App\Models\StockHistory;
use Illuminate\Support\Facades\Auth;

class WareHouseController extends Controller
{
    public function index()
    {

        if (!(in_array('32', json_decode(auth()->user()->role->permission)))) {
          return redirect()->back()->with('error', 'Sorry, You do not have permission to access that page.');
        }

        $data = Warehouse::orderby('id','DESC')->get();
        return view('admin.warehouse.index', compact('data'));
    }

    public function store(Request $request)
    {
        if(empty($request->name)){
            $message ="<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Please fill \" name \" field..!</b></div>";
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        }
        // if(empty($request->location)){
        //     $message ="<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Please fill \" location \" field..!</b></div>";
        //     return response()->json(['status'=> 303,'message'=>$message]);
        //     exit();
        // }
        // if(empty($request->operator_name)){
        //     $message ="<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Please fill \" operator name \" field..!</b></div>";
        //     return response()->json(['status'=> 303,'message'=>$message]);
        //     exit();
        // }
        $chkname = Warehouse::where('name',$request->name)->first();
        if($chkname){
            $message ="<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>This warehouse already added.</b></div>";
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        }
        
        $data = new Warehouse;
        $data->name = $request->name;
        $data->location = $request->location;
        $data->operator_name = $request->operator_name;
        $data->operator_phone = $request->operator_phone;
        $data->description = $request->description;
        $data->created_by = auth()->id(); 
        
        if ($data->save()) {
            $message ="<div class='alert alert-success'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Data Create Successfully.</b></div>";
            return response()->json(['status'=> 300,'message'=>$message,'data'=>$data]);
        }else{
            return response()->json(['status'=> 303,'message'=>'Server Error!!']);
        }
    }

    public function edit($id)
    {
        $where = [
            'id'=>$id
        ];
        $info = Warehouse::where($where)->get()->first();
        return response()->json($info);
    }

    public function update(Request $request)
    {
        if(empty($request->name)){
            $message ="<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Please fill \" name \" field..!</b></div>";
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        }
        // if(empty($request->location)){
        //     $message ="<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Please fill \" location \" field..!</b></div>";
        //     return response()->json(['status'=> 303,'message'=>$message]);
        //     exit();
        // }
        // if(empty($request->operator_name)){
        //     $message ="<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Please fill \" operator name \" field..!</b></div>";
        //     return response()->json(['status'=> 303,'message'=>$message]);
        //     exit();
        // }

        $duplicatename = Warehouse::where('name',$request->name)->where('id','!=', $request->codeid)->first();
        if($duplicatename){
            $message ="<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>warehouse already added.</b></div>";
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        }

         $data = Warehouse::find($request->codeid);
         $data->name = $request->name;
         $data->location = $request->location;
         $data->operator_name = $request->operator_name;
         $data->operator_phone = $request->operator_phone;
         $data->description = $request->description;    
         $data->updated_by = auth()->id();

          if ($data->save()) {
            $message = "<div class='alert alert-success'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Data Updated Successfully.</b></div>";
            return response()->json(['status' => 300, 'message' => $message]);
        } else {
            $message = "<div class='alert alert-danger'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Failed to update data. Please try again.</b></div>";
            return response()->json(['status' => 303, 'message' => $message]);
        }

    }

    public function delete($id)
    {
        $data = Warehouse::find($id);
        
        if (!$data) {
            return response()->json(['success' => false, 'message' => 'Not found.'], 404);
        }

        if ($data->delete()) {
            return response()->json(['success' => true, 'message' => 'Deleted successfully.']);
        } else {
            return response()->json(['success' => false, 'message' => 'Failed to delete.'], 500);
        }
    }

    public function toggleStatus(Request $request)
    {
        $category = Warehouse::find($request->category_id);
        if (!$category) {
            return response()->json(['status' => 404, 'message' => 'Not found']);
        }

        $category->status = $request->status;
        $category->save();

        return response()->json(['status' => 200, 'message' => 'Status updated successfully']);
    }

    public function transfer($id)
    {
        $purchase = Purchase::with('purchaseHistory.product')->findOrFail($id);
        $warehouses = Warehouse::orderby('id','DESC')->where('status', 1)->get();
        $warehouseCount = count($warehouses);
        return view('admin.stock.purchase_transfer', compact('purchase', 'warehouses', 'warehouseCount'));
    }

    public function storeWarehouse(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'operator_name' => 'required|string|max:255',
            'operator_phone' => 'required|numeric',
        ]);

        $warehouse = Warehouse::create($validatedData);

        return response()->json([
            'success' => true,
            'warehouse' => $warehouse
        ]);
    }

    public function transferToWarehouse(Request $request, $purchaseId)
    {
        $request->validate([
            'quantities.*' => 'required|array',
            'warehouses.*' => 'required|array',
        ]);
    
        foreach ($request->quantities as $historyId => $quantities) {
            foreach ($quantities as $index => $quantity) {
                $warehouseId = $request->warehouses[$historyId][$index];
    
                $purchaseHistory = PurchaseHistory::find($historyId);
                $purchase = Purchase::withCount('purchaseHistory')->where('id', $purchaseHistory->purchase_id)->first();

                
                $additionalCost = $purchase->direct_cost + $purchase->cnf_cost + $purchase->cost_a + $purchase->cost_b + $purchase->other_cost;
                $qty = $purchaseHistory->quantity - $purchaseHistory->missing_product_quantity;
                $additionalCostPerProduct = $additionalCost/$purchase->purchase_history_count;
                $additionalCostPerUnit = $additionalCostPerProduct/$qty;

                if (!$purchaseHistory) {
                    continue;
                }
                $purchaseHistory->remaining_product_quantity -= $quantity;
                $purchaseHistory->transferred_product_quantity += $quantity;
                $purchaseHistory->save();

                $size = $request->sizes[$historyId][0];
                $color = $request->colors[$historyId][0];
                $warehouseId = $request->warehouses[$historyId][0];
                
    
                $stock = Stock::where('product_id', $purchaseHistory->product_id)
                    ->where('size', $size)
                    ->where('color', $color)
                    ->where('warehouse_id', $warehouseId)
                    ->first();
    
                if ($stock) {
                    $stock->quantity += $quantity;
                    $stock->updated_by = auth()->id();
                    $stock->save();
                } else {
                    $stock = Stock::create([
                        'product_id' => $purchaseHistory->product_id,
                        'quantity' => $quantity,
                        'size' => $size,
                        'color' => $color,
                        'created_by' => auth()->id(),
                        'warehouse_id' => $warehouseId,
                    ]);
                }

                $stockhistory = new StockHistory();
                $stockhistory->product_id = $purchaseHistory->product_id;
                $stockhistory->purchase_id = $purchaseHistory->purchase_id;
                $stockhistory->stock_id = $stock->id;
                $stockhistory->warehouse_id = $warehouseId;
                $stockhistory->selling_qty = 0;
                $stockhistory->quantity = $quantity;
                $stockhistory->available_qty = $quantity;
                $stockhistory->size = $purchaseHistory->product_size;
                $stockhistory->color = $purchaseHistory->product_color;
                $stockhistory->date = date('Y-m-d');
                $stockhistory->stockid = date('mds').$warehouseId.str_pad($purchaseHistory->id, 4, '0', STR_PAD_LEFT);

                $stockhistory->purchase_price = $purchaseHistory->purchase_price + $additionalCostPerUnit;
                $stockhistory->selling_price = $stockhistory->purchase_price + $stockhistory->purchase_price * .2;
                $stockhistory->created_by = Auth::user()->id;
                $stockhistory->save();
            }
        }
    
        return redirect()->back()->with('success', 'Stock transferred successfully.');
    }

}