<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SubCategory;
use App\Models\Category;
use Illuminate\Support\Str;

class SubCategoryController extends Controller
{
    public function getSubCategory()
    {
        $categories = Category::select('id', 'name')->get();
        $data = SubCategory::orderby('id','DESC')->get();
        return view('admin.sub_category.index', compact('data','categories'));
    }

    public function subCategoryStore(Request $request)
    {
        if(empty($request->category_id)){
            $message ="<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Please fill \"Category \" field..!</b></div>";
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        }
        if(empty($request->name)){
            $message ="<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Please fill \"Sub category name \" field..!</b></div>";
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        }
        $chkname = SubCategory::where('name',$request->name)->first();
        if($chkname){
            $message ="<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>This sub category already added.</b></div>";
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        }
        
        $data = new SubCategory;
        $data->name = $request->name;
        $data->category_id = $request->category_id;
        $data->description = $request->description;
        $data->slug = Str::slug($request->name);
        $data->created_by = auth()->id(); 
        
        if ($data->save()) {
            $message ="<div class='alert alert-success'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Data Create Successfully.</b></div>";
            return response()->json(['status'=> 300,'message'=>$message]);
        }else{
            return response()->json(['status'=> 303,'message'=>'Server Error!!']);
        }
    }

    public function subCategoryEdit($id)
    {
        $where = [
            'id'=>$id
        ];
        $info = SubCategory::where($where)->get()->first();
        return response()->json($info);
    }

    public function subCategoryUpdate(Request $request)
    {
        if(empty($request->category_id)){
            $message ="<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Please fill \"Category \" field..!</b></div>";
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        }
        if(empty($request->name)){
            $message ="<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Please fill \"Sub category name \" field..!</b></div>";
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        }

        $duplicatename = SubCategory::where('name',$request->name)->where('id','!=', $request->codeid)->first();
        if($duplicatename){
            $message ="<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>This sub category already added.</b></div>";
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        }

         $brand = SubCategory::find($request->codeid);
         $brand->name = $request->name;     
         $brand->category_id = $request->category_id;
         $brand->description = $request->description;        
         $brand->updated_by = auth()->id();

          if ($brand->save()) {
            $message = "<div class='alert alert-success'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Data Updated Successfully.</b></div>";
            return response()->json(['status' => 300, 'message' => $message]);
        } else {
            $message = "<div class='alert alert-danger'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Failed to update data. Please try again.</b></div>";
            return response()->json(['status' => 303, 'message' => $message]);
        }

    }

    public function subCategoryDelete($id)
    {
        $brand = SubCategory::find($id);
        
        if (!$brand) {
            return response()->json(['success' => false, 'message' => 'Not found.'], 404);
        }

        if ($brand->delete()) {
            return response()->json(['success' => true, 'message' => 'Deleted successfully.']);
        } else {
            return response()->json(['success' => false, 'message' => 'Failed to delete.'], 500);
        }
    }

    public function toggleStatus(Request $request)
    {
        $category = SubCategory::find($request->category_id);
        if (!$category) {
            return response()->json(['status' => 404, 'message' => 'Not found']);
        }

        $category->status = $request->status;
        $category->save();

        return response()->json(['status' => 200, 'message' => 'Status updated successfully']);
    }

    public function store(Request $request)
    {
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
        ]);

        $existingSubCategory = SubCategory::where('name', $request->name)
            ->where('category_id', $request->category_id)
            ->first();

        if ($existingSubCategory) {
            return response()->json([
                'message' => 'Subcategory already exists for this category!',
                'id' => $existingSubCategory->id,
                'name' => $existingSubCategory->name,
            ], 409);
        }

        $subcategory = SubCategory::create([
            'category_id' => $request->category_id,
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'created_by' => auth()->id(),
        ]);

        return response()->json([
            'id' => $subcategory->id,
            'name' => $subcategory->name,
            'category_id' => $subcategory->category_id,
        ]);
    }

}
