<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ProductModel;
use Illuminate\Support\Str;

class ProductModelController extends Controller
{
    public function getModel()
    {
        $data = ProductModel::orderBy('id', 'DESC')->get();
        return view('admin.model.index', compact('data'));
    }

    public function modelStore(Request $request)
    {
        if (empty($request->name)) {
            $message = "<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Please fill \"Model name\" field..!</b></div>";
            return response()->json(['status' => 303, 'message' => $message]);
            exit();
        }

        $data = new ProductModel;
        $data->name = $request->name;
        $data->slug = Str::slug($request->name);
        $data->created_by = auth()->id();

        if ($data->save()) {
            $message = "<div class='alert alert-success'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Data Created Successfully.</b></div>";
            return response()->json(['status' => 300, 'message' => $message]);
        } else {
            return response()->json(['status' => 303, 'message' => 'Server Error!!']);
        }
    }

    public function modelEdit($id)
    {
        $where = ['id' => $id];
        $info = ProductModel::where($where)->get()->first();
        return response()->json($info);
    }

    public function modelUpdate(Request $request)
    {
        if (empty($request->name)) {
            $message = "<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Please fill \"Model name\" field..!</b></div>";
            return response()->json(['status' => 303, 'message' => $message]);
            exit();
        }

        $model = ProductModel::find($request->codeid);
        $model->name = $request->name;
        $model->updated_by = auth()->id();

        if ($model->save()) {
            $message = "<div class='alert alert-success'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Data Updated Successfully.</b></div>";
            return response()->json(['status' => 300, 'message' => $message]);
        } else {
            $message = "<div class='alert alert-danger'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Failed to update data. Please try again.</b></div>";
            return response()->json(['status' => 303, 'message' => $message]);
        }
    }

    public function modelDelete($id)
    {
        $model = ProductModel::find($id);

        if (!$model) {
            return response()->json(['success' => false, 'message' => 'Not found.'], 404);
        }

        if ($model->delete()) {
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

        $existingModel = ProductModel::where('name', $request->name)->first();

        if ($existingModel) {
            return response()->json([
                'message' => 'Model already exists!',
                'id' => $existingModel->id,
                'name' => $existingModel->name,
            ], 409);
        }

        $model = ProductModel::create([
            'name' => $request->name,
        ]);

        return response()->json([
            'id' => $model->id,
            'name' => $model->name,
        ]);
    }

}
