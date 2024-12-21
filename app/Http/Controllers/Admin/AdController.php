<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Ad;

class AdController extends Controller
{
    public function getAds()
    {
        $data = Ad::orderby('id','DESC')->get();
        $existingTypes = Ad::pluck('type')->unique()->toArray();

        $allowedTypes = [
            'homepage_modal',
            'home_page_top_bar',
            'featured',
            'recent',
            'vendor',
            'home_footer'
        ];

        $availableTypes = array_diff($allowedTypes, $existingTypes);
        return view('admin.ad.index', compact('data', 'availableTypes'));
    }

    public function adStore(Request $request)
    {
        if(empty($request->type)){
            $message ="<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Please fill \"type \" field..!</b></div>";
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        }
         if(empty($request->link)){
            $message ="<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Please fill \"link \" field..!</b></div>";
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        }

        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);
        
        $data = new Ad;
        $data->type = $request->type;
        $data->link = $request->link;
        $data->created_by = auth()->id(); 

        if ($request->hasFile('image')) {
            $uploadedFile = $request->file('image');
            $randomName = mt_rand(10000000, 99999999). '.'. $uploadedFile->getClientOriginalExtension();
            $destinationPath = public_path('images/ads/');
            $path = $uploadedFile->move($destinationPath, $randomName); 
            $data->image = $randomName;
        }
        
        if ($data->save()) {
            $message ="<div class='alert alert-success'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Data Create Successfully.</b></div>";
            return response()->json(['status'=> 300,'message'=>$message]);
        }else{
            return response()->json(['status'=> 303,'message'=>'Server Error!!']);
        }
    }

    public function adEdit($id)
    {
        $where = [
            'id'=>$id
        ];
        $info = Ad::where($where)->get()->first();
        return response()->json($info);
    }

    public function adUpdate(Request $request)
    {
        if(empty($request->link)){
            $message ="<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Please fill \"link \" field..!</b></div>";
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        }
        
        $data = Ad::find($request->codeid);
        // $data->type = $request->type;
        $data->link = $request->link;
        $data->updated_by = auth()->id(); 

        if ($request->hasFile('image')) {
            $uploadedFile = $request->file('image');

            if ($data->image && file_exists(public_path('images/ads/'. $data->image))) {
                unlink(public_path('images/ads/'. $data->image));
            }

            $randomName = mt_rand(10000000, 99999999). '.'. $uploadedFile->getClientOriginalExtension();
            $destinationPath = public_path('images/ads/');
            $path = $uploadedFile->move($destinationPath, $randomName); 
            $data->image = $randomName;
        }
        
        if ($data->save()) {
            $message ="<div class='alert alert-success'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Updated Successfully.</b></div>";
            return response()->json(['status'=> 300,'message'=>$message]);
        }else{
            return response()->json(['status'=> 303,'message'=>'Server Error!!']);
        }
    }

    public function adDelete($id)
    {
        $brand = Ad::find($id);
        
        if (!$brand) {
            return response()->json(['success' => false, 'message' => 'Not found.'], 404);
        }

        if ($brand->image && file_exists(public_path('images/ads/' . $brand->image))) {
            unlink(public_path('images/ads/' . $brand->image));
        }

        if ($brand->delete()) {
            return response()->json(['success' => true, 'message' => 'Deleted successfully.']);
        } else {
            return response()->json(['success' => false, 'message' => 'Failed to delete.'], 500);
        }
    }

    public function toggleStatus(Request $request)
    {
        $ad = Ad::find($request->ad_id);
        if (!$ad) {
            return response()->json(['status' => 404, 'message' => 'Not found']);
        }

        $ad->status = $request->status;
        $ad->save();

        return response()->json(['status' => 200, 'message' => 'Status updated successfully']);
    }
}
