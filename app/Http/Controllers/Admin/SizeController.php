<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Size;
use Illuminate\Http\Request;

class SizeController extends Controller
{
    public function index()
    {
        $data = Size::orderby('id','DESC')->get();
        return view('admin.size.index', compact('data'));
    }

    public function store(Request $request)
    {
        if(empty($request->size)){
            $message ="<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Please fill \"Size \" field..!</b></div>";
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        }
        $chkSize = Size::where('size',$request->size)->first();
        if($chkSize){
            $message ="<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>This size was already added.</b></div>";
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        }
        
        $data = new Size;
        $data->size = $request->size;
        $data->price = $request->price;

        if ($request->hasFile('image')) {
            $uploadedFile = $request->file('image');
            $randomName = mt_rand(10000000, 99999999). '.'. $uploadedFile->getClientOriginalExtension();
            $destinationPath = public_path('images/size/');
            $path = $uploadedFile->move($destinationPath, $randomName); 
            $data->image = '/images/size/' . $randomName;
        }

        $data->created_by = auth()->id(); 
        
        if ($data->save()) {
            $message ="<div class='alert alert-success'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Data Created Successfully.</b></div>";
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
        $info = Size::where($where)->get()->first();
        return response()->json($info);
    }

    public function update(Request $request)
    {
        if(empty($request->size)){
            $message ="<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Please fill \"Size \" field..!</b></div>";
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        }
        $duplicateSize = Size::where('size',$request->size)->where('id','!=', $request->codeid)->first();
        if($duplicateSize){
            $message ="<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>This size was already added.</b></div>";
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        }

         $data = Size::find($request->codeid);
         $data->size = $request->size;
         $data->price = $request->price;

         if ($request->hasFile('image')) {
            if ($data->image) {
                $oldImagePath = public_path($data->image);
                if (file_exists($oldImagePath)) {
                    unlink($oldImagePath);
                }
            }

            $uploadedFile = $request->file('image');
            $randomName = mt_rand(10000000, 99999999) . '.' . $uploadedFile->getClientOriginalExtension();
            $destinationPath = public_path('images/size/');
            $uploadedFile->move($destinationPath, $randomName);
            $data->image = '/images/size/' . $randomName;
         }

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
        $data = Size::find($id);
        
        if (!$data) {
            return response()->json(['success' => false, 'message' => 'Not found.'], 404);
        }

        if ($data->image) {
            $oldImagePath = public_path($data->image);
            if (file_exists($oldImagePath)) {
                unlink($oldImagePath);
            }
        }

        if ($data->delete()) {
            return response()->json(['success' => true, 'message' => 'Deleted successfully.']);
        } else {
            return response()->json(['success' => false, 'message' => 'Failed to delete.'], 500);
        }
    }

    public function toggleStatus(Request $request)
    {
        $size = Size::find($request->size_id);
        if (!$size) {
            return response()->json(['status' => 404, 'message' => 'Color not found']);
        }

        $size->status = $request->status;
        $size->save();

        return response()->json(['status' => 200, 'message' => 'Color status updated successfully']);
    }

    public function storeSize(Request $request)
    {
        $request->validate([
            'size' => 'required|string|max:255|unique:sizes,size',
            'price' => 'nullable|numeric',
        ]);

        $size = new Size();
        $size->size = $request->size;
        $size->price = $request->price;
        $size->save();

        return response()->json(['success' => true, 'data' => $size]);
    }


}
