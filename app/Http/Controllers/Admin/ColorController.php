<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Color;
use Illuminate\Http\Request;

class ColorController extends Controller
{
    public function index()
    {
        $data = Color::orderby('id','DESC')->get();
        return view('admin.color.index', compact('data'));
    }

    public function store(Request $request)
    {
        if(empty($request->color)){
            $message ="<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Please fill \"Color \" field..!</b></div>";
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        }
        $chkcolor = Color::where('color',$request->color)->first();
        if($chkcolor){
            $message ="<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>This color was already added.</b></div>";
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        }
        
        $data = new Color;
        $data->color = $request->color;
        $data->color_code = $request->color_code;
        $data->price = $request->price;

        if ($request->hasFile('image')) {
            $uploadedFile = $request->file('image');
            $randomName = mt_rand(10000000, 99999999). '.'. $uploadedFile->getClientOriginalExtension();
            $destinationPath = public_path('images/size/');
            $path = $uploadedFile->move($destinationPath, $randomName); 
            $data->image = '/images/color/' . $randomName;
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
        $info = Color::where($where)->get()->first();
        return response()->json($info);
    }

    public function update(Request $request)
    {
        if(empty($request->color)){
            $message ="<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Please fill \"Color \" field..!</b></div>";
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        }
        $duplicateColor = Color::where('color',$request->color)->where('id','!=', $request->codeid)->first();
        if($duplicateColor){
            $message ="<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>This color was already added.</b></div>";
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        }

         $data = Color::find($request->codeid);
         $data->color = $request->color;
         $data->color_code = $request->color_code;
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
            $destinationPath = public_path('images/color/');
            $uploadedFile->move($destinationPath, $randomName);
            $data->image = '/images/color/' . $randomName;
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
        $data = Color::find($id);
        
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
        $size = Color::find($request->size_id);
        if (!$size) {
            return response()->json(['status' => 404, 'message' => 'color not found']);
        }

        $size->status = $request->status;
        $size->save();

        return response()->json(['status' => 200, 'message' => 'Color status updated successfully']);
    }

    public function storeColor(Request $request)
    {
        $request->validate([
            'color_name' => 'required|string|max:255|unique:colors,color',
            'color_code' => 'nullable|string|max:255',
            'price' => 'nullable|numeric',
        ]);

        $color = new Color();
        $color->color = $request->color_name;
        $color->color_code = $request->color_code;
        $color->price = $request->price;
        $color->save();

        return response()->json(['success' => true, 'data' => $color]);
    }


}
