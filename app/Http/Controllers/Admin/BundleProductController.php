<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BundleProduct;
use App\Models\Product;
use App\Models\BundleProductImage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class BundleProductController extends Controller
{
    public function getBundleProduct()
    {
        $products = Product::select('id', 'name', 'price')->get();
        $bundleProducts = BundleProduct::all();
        return view('admin.bundle_product.index', compact('bundleProducts', 'products'));
    }

    public function bundleProductStore(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'product_ids' => 'required|array|min:1',
            'short_description' => 'nullable|string',
            'long_description' => 'nullable|string',
            'total_price' => 'required',
            'price' => 'required',
            'feature_image' => 'required|image|max:2048',
            'images.*' => 'image|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $bundleProduct = new BundleProduct();
        $bundleProduct->name = $request->name;
        $bundleProduct->slug = Str::slug($request->name);
        $bundleProduct->product_ids = json_encode($request->product_ids);
        $bundleProduct->short_description = $request->short_description;
        $bundleProduct->long_description = $request->long_description;
        $bundleProduct->total_price = $request->total_price;
        $bundleProduct->quantity = $request->quantity;
        $bundleProduct->price = $request->price;
        $bundleProduct->created_by = auth()->user()->id;

        if ($request->hasFile('feature_image')) {
            $image = $request->file('feature_image');
            $filename = mt_rand(10000000, 99999999) . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('images/bundle_product'), $filename);
            $bundleProduct->feature_image = $filename;
        }

        $bundleProduct->save();

        if ($request->hasFile('images')) {
            $images = $request->file('images');
            foreach ($images as $index => $image) {
                $filename = mt_rand(10000000, 99999999) . '_' . $index . '.' . $image->getClientOriginalExtension();
                $image->move(public_path('images/bundle_product_images'), $filename);

                $bundleProductImage = new BundleProductImage;
                $bundleProductImage->bundle_product_id = $bundleProduct->id;
                $bundleProductImage->image = $filename;
                $bundleProductImage->created_by = auth()->user()->id;
                $bundleProductImage->save();
            }
        }

        return response()->json(['message' => 'Bundle product created successfully', 'data' => $bundleProduct]);
    }

    public function bundleProductEdit($id)
    {
        $info = BundleProduct::with('images')->where('id', $id)->first();
        return response()->json($info);
    }

    public function bundleProductUpdate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'product_ids' => 'required|array|min:1',
            'short_description' => 'nullable|string',
            'long_description' => 'nullable|string',
            'total_price' => 'required|numeric',
            'price' => 'required|numeric',
            'feature_image' => 'nullable|image|max:2048',
            'images.*' => 'nullable|image|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $bundleProduct = BundleProduct::find($request->codeid);

        if ($request->hasFile('feature_image')) {
            if ($bundleProduct->feature_image && file_exists(public_path('images/bundle_product/' . $bundleProduct->feature_image))) {
                unlink(public_path('images/bundle_product/' . $bundleProduct->feature_image));
            }

            $image = $request->file('feature_image');
            $filename = mt_rand(10000000, 99999999) . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('images/bundle_product'), $filename);
            $bundleProduct->feature_image = $filename;
        }

        $bundleProduct->name = $request->name;
        $bundleProduct->slug = Str::slug($request->name);
        $bundleProduct->product_ids = json_encode($request->product_ids);
        $bundleProduct->short_description = $request->short_description;
        $bundleProduct->long_description = $request->long_description;
        $bundleProduct->total_price = $request->total_price;
        $bundleProduct->price = $request->price;
        $bundleProduct->quantity = $request->quantity;
        $bundleProduct->updated_by = auth()->user()->id;

        $bundleProduct->save();

        if ($request->hasFile('images') || $request->has('existing_images')) {
            $existingImages = $request->input('existing_images', []);
            $oldImages = BundleProductImage::where('bundle_product_id', $bundleProduct->id)->get();

            foreach ($oldImages as $oldImage) {
                if (!in_array($oldImage->image, $existingImages)) {
                    if (file_exists(public_path('images/bundle_product_images/' . $oldImage->image))) {
                        unlink(public_path('images/bundle_product_images/' . $oldImage->image));
                    }
                    $oldImage->delete();
                }
            }

            if ($request->hasFile('images')) {
                $images = $request->file('images');
                foreach ($images as $index => $image) {
                    $filename = mt_rand(10000000, 99999999) . '_' . $index . '.' . $image->getClientOriginalExtension();
                    $image->move(public_path('images/bundle_product_images'), $filename);

                    $bundleProductImage = new BundleProductImage;
                    $bundleProductImage->bundle_product_id = $bundleProduct->id;
                    $bundleProductImage->image = $filename;
                    $bundleProductImage->created_by = auth()->user()->id;
                    $bundleProductImage->save();
                }
            }
        }

        return response()->json(['message' => 'Bundle product updated successfully', 'data' => $bundleProduct]);
    }

    public function bundleProductDelete($id)
    {
        $bundleProduct = BundleProduct::find($id);

        if (!$bundleProduct) {
            return response()->json(['message' => 'Bundle product not found'], 404);
        }

        if ($bundleProduct->feature_image && file_exists(public_path('images/bundle_product/' . $bundleProduct->feature_image))) {
            unlink(public_path('images/bundle_product/' . $bundleProduct->feature_image));
        }

        $bundleProductImages = BundleProductImage::where('bundle_product_id', $bundleProduct->id)->get();
        foreach ($bundleProductImages as $image) {
            if (file_exists(public_path('images/bundle_product_images/' . $image->image))) {
                unlink(public_path('images/bundle_product_images/' . $image->image));
            }
            $image->delete();
        }

        $bundleProduct->delete();

        return response()->json(['message' => 'Bundle product deleted successfully']);
    }


}
