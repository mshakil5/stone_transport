<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Color;
use App\Models\Size;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\WholeSaleProductSize;
use App\Models\WholeSaleProductColor;
use App\Models\WholeSaleProductPrice;

class WholeSaleProductController extends Controller
{
    public function getWholeSaleProduct()
    {
        $wholeSaleProducts = WholeSaleProduct::all();
        $products = Product::select('id', 'name', 'price')->get();
        $sizes = Size::all();
        $colors = Color::all();
        return view('admin.whole_sale_products.index', compact('wholeSaleProducts', 'products', 'sizes', 'colors'));
    }

    public function wholeSaleProductStore(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'size_ids' => 'required|array',
            'size_ids.*' => 'exists:sizes,id',
            'color_id' => 'required|array',
            'color_id.*' => 'exists:colors,id',
            'image.*' => 'file|image|mimes:jpeg,png,jpg,gif,svg|max:2048'
        ]);

        $wholeSaleProduct = new WholeSaleProduct();
        $wholeSaleProduct->product_id = $request->product_id;
        $wholeSaleProduct->short_description = $request->short_description;
        $wholeSaleProduct->long_description = $request->long_description;
        $wholeSaleProduct->is_featured = $request->has('is_featured');
        $wholeSaleProduct->is_new_arrival = $request->has('is_new_arrival');
        $wholeSaleProduct->is_top_rated = $request->has('is_top_rated');
        $wholeSaleProduct->is_recent = $request->has('is_recent');
        $wholeSaleProduct->is_popular = $request->has('is_popular');
        $wholeSaleProduct->is_trending = $request->has('is_trending');
        $wholeSaleProduct->created_by = auth()->user()->id;

        if ($request->hasFile('feature_image')) {
            $image = $request->file('feature_image');
            $randomName = mt_rand(10000000, 99999999) . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('images/whole_sale_product'), $randomName);
            $wholeSaleProduct->feature_image = '/images/whole_sale_product/' . $randomName;
        }

        $wholeSaleProduct->save();

        foreach ($request->size_ids as $sizeId) {
            $wholeSaleProductSize = new WholeSaleProductSize();
            $wholeSaleProductSize->whole_sale_product_id = $wholeSaleProduct->id;
            $wholeSaleProductSize->size_id = $sizeId;
            $wholeSaleProductSize->created_by = auth()->user()->id;
            $wholeSaleProductSize->save();
        }

        foreach ($request->color_id as $key => $colorId) {
            $wholeSaleProductColor = new WholeSaleProductColor();
            $wholeSaleProductColor->whole_sale_product_id = $wholeSaleProduct->id;
            $wholeSaleProductColor->color_id = $colorId;

            if ($request->hasFile('image.' . $key)) {
                $image = $request->file('image.' . $key);
                $randomName = mt_rand(10000000, 99999999) . '.' . $image->getClientOriginalExtension();
                $image->move(public_path('images/whole_sale_product'), $randomName);
                $wholeSaleProductColor->image = '/images/whole_sale_product/' . $randomName;
            }

            $wholeSaleProductColor->created_by = auth()->user()->id;
            $wholeSaleProductColor->save();
        }

        return response()->json([
            'success' => true,
            'message' => 'Wholesale product created successfully!',
        ]);
    }

    public function destroyWholeSaleProduct($id)
    {
        $wholeSaleProduct = WholeSaleProduct::find($id);

        if (!$wholeSaleProduct) {
            return response()->json([
                'success' => false,
                'message' => 'Wholesale product not found!',
            ], 404);
        }

        if ($wholeSaleProduct->feature_image) {
            $featureImagePath = public_path($wholeSaleProduct->feature_image);
            if (file_exists($featureImagePath)) {
                unlink($featureImagePath);
            }
        }

        $wholeSaleProductSizes = WholeSaleProductSize::where('whole_sale_product_id', $wholeSaleProduct->id)->get();
        foreach ($wholeSaleProductSizes as $size) {
            $size->delete();
        }

        $wholeSaleProductColors = WholeSaleProductColor::where('whole_sale_product_id', $wholeSaleProduct->id)->get();
        foreach ($wholeSaleProductColors as $color) {
            if ($color->image) {
                $colorImagePath = public_path($color->image);
                if (file_exists($colorImagePath)) {
                    unlink($colorImagePath);
                }
            }
            $color->delete();
        }

        $wholeSaleProduct->delete();

        return response()->json([
            'success' => true,
            'message' => 'Wholesale product deleted successfully!',
        ]);
    }

    public function getWholeSalePrices(Request $request)
    {
        $prices = WholeSaleProductPrice::where('whole_sale_product_id', $request->product_id)->get();
        return response()->json($prices);
    }

    public function saveWholeSalePrices(Request $request)
    {
        $request->validate([
            'whole_sale_product_id' => 'required|exists:whole_sale_products,id',
            'min_quantity' => 'required|array',
            'max_quantity' => 'required|array',
            'price' => 'required|array',
        ]);

        WholeSaleProductPrice::where('whole_sale_product_id', $request->whole_sale_product_id)->delete();
    
        foreach ($request->min_quantity as $key => $value) {
            WholeSaleProductPrice::create([
                'whole_sale_product_id' => $request->whole_sale_product_id,
                'min_quantity' => $request->min_quantity[$key],
                'max_quantity' => $request->max_quantity[$key],
                'price' => $request->price[$key],
            ]);
        }
    
        return response()->json(['success' => true]);
    }
    
}
