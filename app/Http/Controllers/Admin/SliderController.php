<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Slider;
use Illuminate\Support\Str;

class SliderController extends Controller
{
    public function getSlider()
    {
        $data = Slider::orderby('id','DESC')->get();
        return view('admin.slider.index', compact('data'));
    }

    public function sliderStore(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);
        
        $data = new Slider;
        $data->title = $request->title;
        $data->sub_title = $request->sub_title;
        $data->link = $request->link;
        $data->slug = Str::slug($request->title);
        $data->created_by = auth()->id(); 

        if ($request->hasFile('image')) {
            $uploadedFile = $request->file('image');
            $randomName = mt_rand(10000000, 99999999). '.'. $uploadedFile->getClientOriginalExtension();
            $destinationPath = public_path('images/slider/');
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

    public function sliderEdit($id)
    {
        $where = [
            'id'=>$id
        ];
        $info = Slider::where($where)->get()->first();
        return response()->json($info);
    }

    public function sliderUpdate(Request $request)
    {
         $slider = Slider::find($request->codeid);
         $slider->title = $request->title;
         $slider->sub_title = $request->sub_title;
         $slider->link = $request->link;        
         $slider->updated_by = auth()->id();

         if ($request->hasFile('image')) {
            $uploadedFile = $request->file('image');

            if ($slider->image && file_exists(public_path('images/slider/'. $slider->image))) {
                unlink(public_path('images/slider/'. $slider->image));
            }

            $randomName = mt_rand(10000000, 99999999). '.'. $uploadedFile->getClientOriginalExtension();
            $destinationPath = public_path('images/slider/');
            $path = $uploadedFile->move($destinationPath, $randomName); 
            $slider->image = $randomName;
            $slider->save();
        }

          if ($slider->save()) {
            $message = "<div class='alert alert-success'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Data Updated Successfully.</b></div>";
            return response()->json(['status' => 300, 'message' => $message]);
        } else {
            $message = "<div class='alert alert-danger'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Failed to update data. Please try again.</b></div>";
            return response()->json(['status' => 303, 'message' => $message]);
        }

    }

    public function sliderDelete($id)
    {
        $slider = Slider::find($id);
        
        if (!$slider) {
            return response()->json(['success' => false, 'message' => 'Not found.'], 404);
        }

        if ($slider->image && file_exists(public_path('images/slider/' . $slider->image))) {
            unlink(public_path('images/slider/' . $slider->image));
        }

        if ($slider->delete()) {
            return response()->json(['success' => true, 'message' => 'Deleted successfully.']);
        } else {
            return response()->json(['success' => false, 'message' => 'Failed to delete.'], 500);
        }
    }

    public function toggleStatus(Request $request)
    {
        $slider = slider::find($request->slider_id);
        if (!$slider) {
            return response()->json(['status' => 404, 'message' => 'Not found']);
        }

        $slider->status = $request->status;
        $slider->save();

        return response()->json(['status' => 200, 'message' => 'Status updated successfully']);
    }
}
