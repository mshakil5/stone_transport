<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    public function getCategory()
    {
        $data = Category::orderby('id','DESC')->get();
        return view('admin.category.index', compact('data'));
    }

    public function categoryStore(Request $request)
    {
        if(empty($request->name)){
            $message ="<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Please fill \"Group name \" field..!</b></div>";
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        }
        $chkname = Category::where('name',$request->name)->first();
        if($chkname){
            $message ="<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>This category already added.</b></div>";
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        }
        
        $data = new Category;
        $data->name = $request->name;
        $data->description = $request->description;
        $data->slug = Str::slug($request->name);
        $data->created_by = auth()->id(); 

        if ($request->hasFile('image')) {
            $uploadedFile = $request->file('image');
            $randomName = mt_rand(10000000, 99999999). '.'. $uploadedFile->getClientOriginalExtension();
            $destinationPath = public_path('images/category/');
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

    public function categoryEdit($id)
    {
        $where = [
            'id'=>$id
        ];
        $info = Category::where($where)->get()->first();
        return response()->json($info);
    }

    public function categoryUpdate(Request $request)
    {
        if(empty($request->name)){
            $message ="<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Please fill \"Group name \" field..!</b></div>";
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        }

        $duplicatename = Category::where('name',$request->name)->where('id','!=', $request->codeid)->first();
        if($duplicatename){
            $message ="<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>This category already added.</b></div>";
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        }

         $brand = Category::find($request->codeid);
         $brand->name = $request->name;
         $brand->description = $request->description;        
         $brand->updated_by = auth()->id();

         if ($request->hasFile('image')) {
            $uploadedFile = $request->file('image');

            if ($brand->image && file_exists(public_path('images/category/'. $brand->image))) {
                unlink(public_path('images/category/'. $brand->image));
            }

            $randomName = mt_rand(10000000, 99999999). '.'. $uploadedFile->getClientOriginalExtension();
            $destinationPath = public_path('images/category/');
            $path = $uploadedFile->move($destinationPath, $randomName); 
            $brand->image = $randomName;
            $brand->save();
        }

          if ($brand->save()) {
            $message = "<div class='alert alert-success'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Data Updated Successfully.</b></div>";
            return response()->json(['status' => 300, 'message' => $message]);
        } else {
            $message = "<div class='alert alert-danger'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Failed to update data. Please try again.</b></div>";
            return response()->json(['status' => 303, 'message' => $message]);
        }

    }

    public function categoryDelete($id)
    {
        $brand = Category::find($id);
        
        if (!$brand) {
            return response()->json(['success' => false, 'message' => 'Not found.'], 404);
        }

        if ($brand->image && file_exists(public_path('images/category/' . $brand->image))) {
            unlink(public_path('images/category/' . $brand->image));
        }

        if ($brand->delete()) {
            return response()->json(['success' => true, 'message' => 'Deleted successfully.']);
        } else {
            return response()->json(['success' => false, 'message' => 'Failed to delete.'], 500);
        }
    }

    public function toggleStatus(Request $request)
    {
        $category = Category::find($request->category_id);
        if (!$category) {
            return response()->json(['status' => 404, 'message' => 'Category not found']);
        }

        $category->status = $request->status;
        $category->save();

        return response()->json(['status' => 200, 'message' => 'Category status updated successfully']);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $existingCategory = Category::where('name', $request->name)->first();

        if ($existingCategory) {
            return response()->json([
                'message' => 'Category already exists!',
                'id' => $existingCategory->id,
                'name' => $existingCategory->name,
            ], 409);
        }

        $category = Category::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
        ]);

        return response()->json([
            'id' => $category->id,
            'name' => $category->name,
        ]);
    }


}
