<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\RelatedProduct;
use App\Models\Product;
use Illuminate\Support\Facades\Validator;

class RelatedProductController extends Controller
{
    public function getrelatedProduct()
    {
        $allProductIds = Product::pluck('id')->toArray();
        $relatedProductIds = RelatedProduct::pluck('product_id')->toArray();
        $availableProductIds = array_diff($allProductIds, $relatedProductIds);
        $products = Product::whereIn('id', $availableProductIds)
                        ->select('id', 'name')
                        ->get();

        $relatedProducts = RelatedProduct::orderBy('id', 'desc')->get();

        return view('admin.related_product.index', compact('relatedProducts', 'products'));
    }

    public function relatedProductStore(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_id' => 'required',
            'related_product_ids' => 'required|array|min:1'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $relatedProduct = new RelatedProduct();
        $relatedProduct->product_id = $request->product_id;
        $relatedProduct->related_product_ids = json_encode($request->related_product_ids);
        $relatedProduct->created_by = auth()->user()->id;

        $relatedProduct->save();

        return response()->json(['message' => 'Related product created successfully', 'data' => $relatedProduct]);
    }

    public function relatedProductEdit($id)
    {
        $info = RelatedProduct::with('product')->where('id', $id)->first();
        return response()->json($info);
    }

    public function relatedProductUpdate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'related_product_ids' => 'required|array|min:1'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $relatedProduct = RelatedProduct::find($request->codeid);

        $relatedProduct->related_product_ids = json_encode($request->related_product_ids);
        $relatedProduct->updated_by = auth()->user()->id;

        $relatedProduct->save();

        return response()->json(['message' => 'Related product updated successfully', 'data' => $relatedProduct]);
    }

    public function relatedProductDelete($id)
    {
        $relatedProduct = RelatedProduct::find($id);

        if (!$relatedProduct) {
            return response()->json(['message' => 'Related product not found'], 404);
        }

        $relatedProduct->delete();

        return response()->json(['message' => 'Related product deleted successfully']);
    }
}
