<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BuyOneGetOne;
use App\Models\Product;
use Illuminate\Support\Facades\Validator;
use App\Models\BuyOneGetOneImage;

class BuyOneGetOneController extends Controller
{
    public function get()
    {
        $allProducts = Product::all();
        $allProductIds = Product::pluck('id')->toArray();
        $bogoIds = BuyOneGetOne::pluck('product_id')->toArray();
        $availableProductIds = array_diff($allProductIds, $bogoIds);
        $products = Product::whereIn('id', $availableProductIds)
                            ->select('id', 'name', 'price')
                            ->get();

        $bogoProducts = BuyOneGetOne::orderBy('id', 'desc')->get();

        return view('admin.bogo.index', compact('bogoProducts', 'products', 'allProducts'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_id' => 'required',
            'price' => 'required',
            'quantity' => 'required',
            'get_product_ids' => 'required|array|min:1',
            'short_description' => 'nullable|string',
            'long_description' => 'nullable|string',
            'feature_image' => 'required|image|max:2048',
            'images.*' => 'image|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $buyOneGetOne = new BuyOneGetOne();
        $buyOneGetOne->product_id = $request->product_id;
        $buyOneGetOne->price = $request->price;
        $buyOneGetOne->quantity = $request->quantity;
        $buyOneGetOne->short_description = $request->short_description;
        $buyOneGetOne->long_description = $request->long_description;
        $buyOneGetOne->get_product_ids = json_encode($request->get_product_ids);
        $buyOneGetOne->created_by = auth()->user()->id;

        if ($request->hasFile('feature_image')) {
            $image = $request->file('feature_image');
            $filename = mt_rand(10000000, 99999999) . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('images/buy_one_get_one'), $filename);
            $buyOneGetOne->feature_image = $filename;
        }

        $buyOneGetOne->save();

        if ($request->hasFile('images')) {
            $images = $request->file('images');
            foreach ($images as $index => $image) {
                $filename = mt_rand(10000000, 99999999) . '_' . $index . '.' . $image->getClientOriginalExtension();
                $image->move(public_path('images/buy_one_to_one_product_images'), $filename);

                $buyOneGetOneImage = new BuyOneGetOneImage;
                $buyOneGetOneImage->buy_one_get_one_id = $buyOneGetOne->id;
                $buyOneGetOneImage->image = $filename;
                $buyOneGetOneImage->created_by = auth()->user()->id;
                $buyOneGetOneImage->save();
            }
        }

        return response()->json(['message' => 'Buy One Get One created successfully', 'data' => $buyOneGetOne]);
    }

    public function edit($id)
    {
        $info = BuyOneGetOne::with('product', 'images')->where('id', $id)->first();
        return response()->json($info);
    }

    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'price' => 'required',
            'quantity' => 'required',
            'short_description' => 'nullable|string',
            'long_description' => 'nullable|string',
            'get_product_ids' => 'required|array|min:1',
            'feature_image' => 'nullable|image|max:2048',
            'images.*' => 'nullable|image|max:2048'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $buyOneGetOne = BuyOneGetOne::find($request->codeid);

        if ($request->hasFile('feature_image')) {
            if ($buyOneGetOne->feature_image && file_exists(public_path('images/buy_one_get_one/' . $buyOneGetOne->feature_image))) {
                unlink(public_path('images/buy_one_get_one/' . $buyOneGetOne->feature_image));
            }

            $image = $request->file('feature_image');
            $filename = mt_rand(10000000, 99999999) . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('images/buy_one_get_one'), $filename);
            $buyOneGetOne->feature_image = $filename;
        }

        $buyOneGetOne->price = $request->price;
        $buyOneGetOne->quantity = $request->quantity;
        $buyOneGetOne->short_description = $request->short_description;
        $buyOneGetOne->long_description = $request->long_description;
        $buyOneGetOne->get_product_ids = json_encode($request->get_product_ids);
        $buyOneGetOne->updated_by = auth()->user()->id;

        $buyOneGetOne->save();

        if ($request->hasFile('images') || $request->has('existing_images')) {
            $existingImages = $request->input('existing_images', []);
            $oldImages = BuyOneGetOneImage::where('buy_one_get_one_id', $buyOneGetOne->id)->get();

            foreach ($oldImages as $oldImage) {
                if (!in_array($oldImage->image, $existingImages)) {
                    if (file_exists(public_path('images/buy_one_to_one_product_images/' . $oldImage->image))) {
                        unlink(public_path('images/buy_one_to_one_product_images/' . $oldImage->image));
                    }
                    $oldImage->delete();
                }
            }

            if ($request->hasFile('images')) {
                $images = $request->file('images');
                foreach ($images as $index => $image) {
                    $filename = mt_rand(10000000, 99999999) . '_' . $index . '.' . $image->getClientOriginalExtension();
                    $image->move(public_path('images/buy_one_to_one_product_images'), $filename);

                    $buyOneGetOneImage = new BuyOneGetOneImage;
                    $buyOneGetOneImage->buy_one_get_one_id = $buyOneGetOne->id;
                    $buyOneGetOneImage->image = $filename;
                    $buyOneGetOneImage->created_by = auth()->user()->id;
                    $buyOneGetOneImage->save();
                }
            }
        }

        return response()->json(['message' => 'Updated successfully', 'data' => $buyOneGetOne]);
    }

    public function delete($id)
    {
        $buyOneGetOne = BuyOneGetOne::find($id);

        if (!$buyOneGetOne) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        if ($buyOneGetOne->feature_image && file_exists(public_path('images/buy_one_get_one/' . $buyOneGetOne->feature_image))) {
            unlink(public_path('images/buy_one_get_one/' . $buyOneGetOne->feature_image));
        }

        $buyOneGetOneImage = BuyOneGetOneImage::where('buy_one_get_one_id', $buyOneGetOne->id)->get();
        foreach ($buyOneGetOneImage as $image) {
            if (file_exists(public_path('images/buy_one_to_one_product_images/' . $image->image))) {
                unlink(public_path('images/buy_one_to_one_product_images/' . $image->image));
            }
            $image->delete();
        }

        $buyOneGetOne->delete();

        return response()->json(['message' => 'Deleted successfully']);
    }
}
