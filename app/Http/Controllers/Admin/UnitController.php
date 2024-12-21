<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Unit;
use Illuminate\Support\Str;

class UnitController extends Controller
{
    public function getUnit()
    {
        $data = Unit::orderby('id','DESC')->get();
        return view('admin.unit.index', compact('data'));
    }

    public function unitStore(Request $request)
    {
        if(empty($request->name)){
            $message ="<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Please fill \"Unit name \" field..!</b></div>";
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        }
        
        $data = new Unit;
        $data->name = $request->name;
        $data->slug = Str::slug($request->name);
        $data->created_by = auth()->id(); 
        
        if ($data->save()) {
            $message ="<div class='alert alert-success'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Data Create Successfully.</b></div>";
            return response()->json(['status'=> 300,'message'=>$message]);
        }else{
            return response()->json(['status'=> 303,'message'=>'Server Error!!']);
        }
    }

    public function unitEdit($id)
    {
        $where = [
            'id'=>$id
        ];
        $info = Unit::where($where)->get()->first();
        return response()->json($info);
    }

    public function unitUpdate(Request $request)
    {
        if(empty($request->name)){
            $message ="<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Please fill \"Unit name \" field..!</b></div>";
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        }

         $brand = Unit::find($request->codeid);
         $brand->name = $request->name;
         $brand->updated_by = auth()->id();

          if ($brand->save()) {
            $message = "<div class='alert alert-success'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Data Updated Successfully.</b></div>";
            return response()->json(['status' => 300, 'message' => $message]);
        } else {
            $message = "<div class='alert alert-danger'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Failed to update data. Please try again.</b></div>";
            return response()->json(['status' => 303, 'message' => $message]);
        }

    }

    public function unitDelete($id)
    {
        $brand = Unit::find($id);
        
        if (!$brand) {
            return response()->json(['success' => false, 'message' => 'Not found.'], 404);
        }

        if ($brand->delete()) {
            return response()->json(['success' => true, 'message' => 'Deleted successfully.']);
        } else {
            return response()->json(['success' => false, 'message' => 'Failed to delete.'], 500);
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $existingUnit = Unit::where('name', $request->name)->first();

        if ($existingUnit) {
            return response()->json([
                'message' => 'Unit already exists!',
                'id' => $existingUnit->id,
                'name' => $existingUnit->name,
            ], 409);
        }

        $unit = Unit::create([
            'name' => $request->name,
        ]);

        return response()->json([
            'id' => $unit->id,
            'name' => $unit->name,
        ]);
    }

}
