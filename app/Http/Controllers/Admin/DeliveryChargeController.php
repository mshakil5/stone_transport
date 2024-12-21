<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DeliveryCharge;

class DeliveryChargeController extends Controller
{
    public function index()
    {
        $data = DeliveryCharge::orderby('id','DESC')->get();
        return view('admin.delivery_charge.index', compact('data'));
    }

    public function store(Request $request)
    {
        if (empty($request->min_price)) {
            $message = "<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Please fill \"min price\" field..!</b></div>";
            return response()->json(['status' => 303, 'message' => $message]);
        }
        if (!isset($request->delivery_charge)) {
            $message = "<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Please fill \"delivery charge\" field..!</b></div>";
            return response()->json(['status' => 303, 'message' => $message]);
        }
        if ($request->max_price <= $request->min_price) {
            $message = "<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>The \"max price\" must be greater than \"min price\".</b></div>";
            return response()->json(['status' => 303, 'message' => $message]);
        }
        $existingCharge = DeliveryCharge::where(function ($query) use ($request) {
            $query->whereBetween('min_price', [$request->min_price, $request->max_price])
                  ->orWhereBetween('max_price', [$request->min_price, $request->max_price])
                  ->orWhere(function ($q) use ($request) {
                      $q->where('min_price', '<=', $request->min_price)
                        ->where('max_price', '>=', $request->max_price);
                  });
        })->first();
    
        if ($existingCharge) {
            $message = "<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>The price range overlaps with an existing delivery charge. Please choose a different range.</b></div>";
            return response()->json(['status' => 303, 'message' => $message]);
        }
    
        $data = new DeliveryCharge;
        $data->min_price = $request->min_price;
        $data->max_price = $request->max_price;
        $data->delivery_charge = $request->delivery_charge;
        $data->created_by = auth()->id(); 
    
        if ($data->save()) {
            $message = "<div class='alert alert-success'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Data Created Successfully.</b></div>";
            return response()->json(['status' => 300, 'message' => $message]);
        } else {
            return response()->json(['status' => 303, 'message' => 'Server Error!!']);
        }
    }

    public function edit($id)
    {
        $where = [
            'id'=>$id
        ];
        $info = DeliveryCharge::where($where)->get()->first();
        return response()->json($info);
    }

    public function update(Request $request)
    {
        $data = DeliveryCharge::find($request->codeid);

        if (!$data) {
            return response()->json(['status' => 404, 'message' => 'Delivery charge not found.']);
        }

        if (empty($request->min_price)) {
            $message = "<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Please fill \"min price\" field..!</b></div>";
            return response()->json(['status' => 303, 'message' => $message]);
        }

        if (!isset($request->delivery_charge)) {
            $message = "<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Please fill \"delivery charge\" field..!</b></div>";
            return response()->json(['status' => 303, 'message' => $message]);
        }

        if ($request->max_price <= $request->min_price) {
            $message = "<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>The \"max price\" must be greater than \"min price\".</b></div>";
            return response()->json(['status' => 303, 'message' => $message]);
        }

        $existingCharge = DeliveryCharge::where(function ($query) use ($request, $data) {
            $query->whereBetween('min_price', [$request->min_price, $request->max_price])
                ->orWhereBetween('max_price', [$request->min_price, $request->max_price])
                ->orWhere(function ($q) use ($request) {
                    $q->where('min_price', '<=', $request->min_price)
                        ->where('max_price', '>=', $request->max_price);
                });
        })->where('id', '!=', $request->codeid)->first();

        if ($existingCharge) {
            $message = "<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>The price range overlaps with an existing delivery charge. Please choose a different range.</b></div>";
            return response()->json(['status' => 303, 'message' => $message]);
        }

        $data->min_price = $request->min_price;
        $data->max_price = $request->max_price;
        $data->delivery_charge = $request->delivery_charge;
        $data->updated_by = auth()->id();
    
        if ($data->save()) {
            $message = "<div class='alert alert-success'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Data updated successfully.</b></div>";
            return response()->json(['status' => 300, 'message' => $message]);
        } else {
            return response()->json(['status' => 303, 'message' => 'Server Error!!']);
        }
    }

    public function delete($id)
    {
        $data = DeliveryCharge::find($id);
        
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
