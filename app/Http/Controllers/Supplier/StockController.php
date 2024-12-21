<?php

namespace App\Http\Controllers\Supplier;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SupplierStock;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;

class StockController extends Controller
{
    public function index()
    {
        $supplierId = Auth::guard('supplier')->user()->id;
        $existingProductIds = SupplierStock::pluck('product_id')->toArray();
        $products = Product::whereNotIn('id', $existingProductIds)
                       ->orderBy('id', 'DESC')
                       ->select('id', 'name', 'price')
                       ->get();
        $data = SupplierStock::with('product')
                    ->where('supplier_id', $supplierId)
                    ->orderBy('id', 'desc')
                    ->get();
        return view('supplier.stock', compact('data', 'products'));
    }

    public function store(Request $request)
    {

        if(empty($request->product_id)){
            $message ="<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Please select \"Product \" field..!</b></div>";
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        }
        if(empty($request->size)){
            $message ="<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Please select \"Size \" field..</b></div>";
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        }
        if(empty($request->color)){
            $message ="<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Please select \"Color \" field..</b></div>";
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        }
        if(empty($request->quantity)){            
            $message ="<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Please fill \"Quantity\" field..!</b></div>"; 
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        }

        $data = new SupplierStock;
        $data->product_id = $request->product_id;
        $data->supplier_id = Auth::guard('supplier')->user()->id;
        $data->quantity = $request->quantity;
        $data->size = $request->size;
        $data->color = $request->color;
        $data->price = $request->price;
        $data->description = $request->description;
        $data->created_by = Auth::guard('supplier')->user()->id;
        if ($data->save()) {
            $message ="<div class='alert alert-success'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Data Create Successfully.</b></div>";
            return response()->json(['status'=> 300,'message'=>$message]);
        }else{
            return response()->json(['status'=> 303,'message'=>'Server Error!!']);
        }

    }

    public function edit($id)
    {
        $where = [
            'id'=>$id
        ];
        $info = SupplierStock::where($where)->get()->first();
        return response()->json($info);
    }

    public function update(Request $request)
    {

        if(empty($request->size)){
            $message ="<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Please select \"Size \" field..</b></div>";
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        }
        if(empty($request->color)){
            $message ="<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Please select \"Color \" field..</b></div>";
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        }
        if(empty($request->quantity)){            
            $message ="<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Please fill \"Quantity\" field..!</b></div>"; 
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        }

        $data = SupplierStock::find($request->codeid);
        $data->quantity = $request->quantity;
        $data->size = $request->size;
        $data->color = $request->color;
        $data->price = $request->price;
        $data->description = $request->description;
        $data->created_by = Auth::guard('supplier')->user()->id;
        if ($data->save()) {
            $message ="<div class='alert alert-success'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Data Update Successfully.</b></div>";
            return response()->json(['status'=> 300,'message'=>$message]);
        }else{
            return response()->json(['status'=> 303,'message'=>'Server Error!!']);
        }

    }

    public function delete($id)
    {
        $data = SupplierStock::find($id);
        
        if (!$data) {
            return response()->json(['success' => false, 'message' => 'Not found.'], 404);
        }

        if ($data->delete()) {
            return response()->json(['success' => true, 'message' => 'Deleted successfully.']);
        } else {
            return response()->json(['success' => false, 'message' => 'Failed to delete.'], 500);
        }
    }
}
