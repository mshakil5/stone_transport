<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use App\Models\ProductModel;
use App\Models\Group;
use App\Models\Unit;
use App\Models\Color;
use App\Models\Size;
use App\Models\ProductImage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use App\Models\CompanyDetails;
use App\Models\SubCategory;
use App\Models\ProductSize;
use App\Models\ProductColor;
use App\Models\ProductPrice;
use App\Models\ProductReview;

class ProductController extends Controller
{
    public function getProduct()
    {

        if (!(in_array('3', json_decode(auth()->user()->role->permission)))) {
          return redirect()->back()->with('error', 'Sorry, You do not have permission to access that page.');
        }
        
        $data = Product::productSellingPriceCal();
        return view('admin.product.index', compact('data'));
    }

    public function createProduct()
    {

        if (!(in_array('2', json_decode(auth()->user()->role->permission)))) {
          return redirect()->back()->with('error', 'Sorry, You do not have permission to access that page.');
        }

        $brands = Brand::select('id', 'name')->orderby('id','DESC')->get();
        $product_models = ProductModel::select('id', 'name')->orderby('id','DESC')->get();
        $groups = Group::select('id', 'name')->orderby('id','DESC')->get();
        $units = Unit::select('id', 'name')->orderby('id','DESC')->get();
        $categories = Category::select('id', 'name')->orderby('id','DESC')->get();
        $subCategories = SubCategory::select('id', 'name', 'category_id')->orderby('id','DESC')->get();
        $sizes = Size::select('id', 'size')->orderby('id','DESC')->get();
        $colors = Color::select('id', 'color', 'color_code')->orderby('id','DESC')->get();

        return view('admin.product.create', compact('brands', 'product_models', 'groups', 'units', 'categories', 'subCategories', 'sizes', 'colors'));
    }

    public function productEdit($id)
    {
        $product = Product::with('colors', 'sizes')->findOrFail($id);
        $brands = Brand::select('id', 'name')->orderby('id','DESC')->get();
        $product_models = ProductModel::select('id', 'name')->orderby('id','DESC')->get();
        $groups = Group::select('id', 'name')->orderby('id','DESC')->get();
        $units = Unit::select('id', 'name')->orderby('id','DESC')->get();
        $categories = Category::select('id', 'name')->orderby('id','DESC')->get();
        $subCategories = SubCategory::select('id', 'name', 'category_id')->orderby('id','DESC')->get();
        $sizes = Size::select('id', 'size')->orderby('id','DESC')->get();
        $colors = Color::select('id', 'color', 'color_code')->orderby('id','DESC')->get();
    
        return view('admin.product.edit', compact('product', 'brands', 'product_models', 'groups', 'units', 'categories', 'subCategories', 'sizes', 'colors'));
    }

    public function productDelete(Request $request)
    {
        $id = $request->input('id');
        
        $product = Product::find($id);
    
        if (!$product) {
            return response()->json(['success' => false, 'message' => 'Product not found.']);
        }
    
        $isInOrderDetails = $product->orderDetails()->exists();
        $isInPurchaseHistories = $product->purchaseHistories()->exists();
    
        if ($isInOrderDetails || $isInPurchaseHistories) {
            $product->status = 2; 
            $product->save();
            return response()->json(['success' => false, 'message' => 'Product is associated with orders or purchases. Status updated to 2.']);
        }
    
        if ($product->feature_image && file_exists(public_path('images/products/' . $product->feature_image))) {
            unlink(public_path('images/products/' . $product->feature_image));
        }
    
        foreach ($product->colors as $color) {
            if ($color->image && file_exists(public_path($color->image))) {
                unlink(public_path($color->image));
            }
            $color->delete();
        }

        $product->delete();
    
        return response()->json(['success' => true, 'message' => 'Product and images deleted successfully.']);
    }

    public function toggleFeatured(Request $request)
    {
        $request->validate([
            'id' => 'required',
            'is_featured' => 'required|boolean'
        ]);

        $product = Product::find($request->id);
        $product->is_featured = $request->is_featured;
        $product->save();
        return response()->json(['message' => 'Featured status updated successfully!']);
    }

    public function toggleRecent(Request $request)
    {
        $request->validate([
            'id' => 'required',
            'is_recent' => 'required|boolean'
        ]);

        $product = Product::find($request->id);
        $product->is_recent = $request->is_recent;
        $product->save();
        return response()->json(['message' => 'Recent status updated successfully!']);
    }

    public function togglePopular(Request $request)
    {
        $request->validate([
            'id' => 'required',
            'is_popular' => 'required|boolean'
        ]);

        $product = Product::find($request->id);
        $product->is_popular = $request->is_popular;
        $product->save();

        return response()->json(['message' => 'Popular status updated successfully!']);
    }

    public function toggleTrending(Request $request)
    {
        $request->validate([
            'id' => 'required',
            'is_trending' => 'required|boolean'
        ]);

        $product = Product::find($request->id);
        $product->is_trending = $request->is_trending;
        $product->save();

        return response()->json(['message' => 'Trending status updated successfully!']);
    }

    public function showProductDetails($id)
    {
        $currency = CompanyDetails::value('currency');
        $product = Product::with(['colors.color', 'sizes', 'category', 'subCategory', 'brand', 'productModel', 'group', 'unit'])->findOrFail($id);
        return view('admin.product.details', compact('product', 'currency'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'product_code' => 'required|string|max:255|unique:products,product_code',
            'price' => 'nullable|numeric',
            'size_ids' => 'nullable|array',
            'size_ids.*' => 'exists:sizes,id',
            'sku' => 'nullable|string|max:255',
            'short_description' => 'nullable|string',
            'long_description' => 'nullable|string',
            'category_id' => 'nullable|exists:categories,id',
            'subcategory_id' => 'nullable|exists:subcategories,id',
            'brand_id' => 'nullable|exists:brands,id',
            'product_model_id' => 'nullable|exists:product_models,id',
            'unit_id' => 'nullable|exists:units,id',
            'group_id' => 'nullable|exists:groups,id',
            'feature_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'image.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $imagePath = null;
        if ($request->hasFile('feature_image')) {
            $image = $request->file('feature_image');
            $randomName = mt_rand(10000000, 99999999) . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('images/products'), $randomName);
            $imagePath = $randomName;
        }

        $product = Product::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'product_code' => $request->product_code,
            'price' => $request->price,
            'sku' => $request->sku,
            'short_description' => $request->short_description,
            'long_description' => $request->long_description,
            'category_id' => $request->category_id,
            'sub_category_id' => $request->subcategory_id,
            'brand_id' => $request->brand_id,
            'product_model_id' => $request->product_model_id,
            'unit_id' => $request->unit_id,
            'group_id' => $request->group_id,
            'feature_image' => $imagePath,
            'created_by' => auth()->user()->id,
            'is_whole_sale' => $request->is_whole_sale ? 1 : 0,
            'is_featured' => $request->is_featured ? 1 : 0,
            'is_recent' => $request->is_recent ? 1 : 0,
            'is_new_arrival' => $request->is_new_arrival ? 1 : 0,
            'is_top_rated' => $request->is_top_rated ? 1 : 0,
            'is_popular' => $request->is_popular ? 1 : 0,
            'is_trending' => $request->is_trending ? 1 : 0,
        ]);

        // foreach ($request->size_ids as $sizeId) {
        //     ProductSize::create([
        //         'product_id' => $product->id,
        //         'size_id' => $sizeId,
        //         'created_by' => auth()->user()->id,
        //     ]);
        // }

        // if ($request->has('color_id')) {
        //     foreach ($request->color_id as $key => $colorId) {
        //         $productColor = new ProductColor();
        //         $productColor->product_id = $product->id;
        //         $productColor->color_id = $colorId;

        //         if ($request->hasFile('image.' . $key)) {
        //             $colorImage = $request->file('image.' . $key);
        //             $randomName = mt_rand(10000000, 99999999) . '.' . $colorImage->getClientOriginalExtension();
        //             $colorImage->move(public_path('images/products'), $randomName);
        //             $productColor->image = '/images/products/' . $randomName;
        //         }

        //         $productColor->created_by = auth()->user()->id;
        //         $productColor->save();
        //     }
        // }

        return response()->json(['message' => 'Product created successfully!', 'product' => $product], 201);
    }

    public function checkProductCode(Request $request)
    {
        $productCode = $request->product_code;
        $productId = $request->product_id;
    
        if ($productId) {
            $exists = Product::where('product_code', $productCode)
                            ->where('id', '!=', $productId)
                            ->exists();
        } else {
            $exists = Product::where('product_code', $productCode)->exists();
        }
    
        return response()->json(['exists' => $exists]);
    }

    public function update(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:products,id',
            'name' => 'required|string|max:255',
            'product_code' => 'required|string|max:255|unique:products,product_code,' . $request->id,
            'price' => 'nullable|numeric',
            'size_ids' => 'nullable|array',
            'size_ids.*' => 'exists:sizes,id',
            'sku' => 'nullable|string|max:255',
            'short_description' => 'nullable|string',
            'long_description' => 'nullable|string',
            'category_id' => 'nullable|exists:categories,id',
            'subcategory_id' => 'nullable|exists:subcategories,id',
            'brand_id' => 'nullable|exists:brands,id',
            'product_model_id' => 'nullable|exists:product_models,id',
            'unit_id' => 'nullable|exists:units,id',
            'group_id' => 'nullable|exists:groups,id',
            'feature_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            // 'color_id' => 'nullable|array',
            // 'color_id.*' => 'exists:colors,id',
            'image.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $product = Product::find($request->id);

        if ($request->hasFile('feature_image')) {
            if ($product->feature_image) {
                $oldImagePath = public_path('images/products/' . $product->feature_image);
                if (file_exists($oldImagePath)) {
                    unlink($oldImagePath); 
                }
            }

            $image = $request->file('feature_image');
            $randomName = mt_rand(10000000, 99999999) . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('images/products'), $randomName);
            $product->feature_image = $randomName;
        }

        $product->name = $request->name;
        $product->slug = Str::slug($request->name);
        $product->product_code = $request->product_code;
        $product->price = $request->price;
        $product->sku = $request->sku;
        $product->short_description = $request->short_description;
        $product->long_description = $request->long_description;
        $product->category_id = $request->category_id;
        $product->sub_category_id = $request->subcategory_id;
        $product->brand_id = $request->brand_id;
        $product->product_model_id = $request->product_model_id;
        $product->unit_id = $request->unit_id;
        $product->group_id = $request->group_id;
        $product->updated_by = auth()->user()->id;
        $product->is_whole_sale = $request->is_whole_sale ? 1 : 0;
        $product->is_featured = $request->is_featured ? 1 : 0;
        $product->is_recent = $request->is_recent ? 1 : 0;
        $product->is_new_arrival = $request->is_new_arrival ? 1 : 0;
        $product->is_top_rated = $request->is_top_rated ? 1 : 0;
        $product->is_popular = $request->is_popular ? 1 : 0;
        $product->is_trending = $request->is_trending ? 1 : 0;

        $product->save();

        // if ($request->has('size_ids')) {
        //     $product->sizes()->sync($request->size_ids);
        // }

        // if ($request->has('color_id')) {
        //     //existing colors
        //     $existingColors = $product->colors;
        
        //     //updated colors
        //     $updatedColorIds = $request->input('color_id', []);
        
        //     // Delete colors that are not in the updated list
        //     foreach ($existingColors as $existingColor) {
        //         if (!in_array($existingColor->color_id, $updatedColorIds)) {
        //             if ($existingColor->image && file_exists(public_path($existingColor->image))) {
        //                 unlink(public_path($existingColor->image));
        //             }
        //             $existingColor->delete();
        //         }
        //     }
        
        //     // Add new colors
        //     foreach ($updatedColorIds as $key => $colorId) {
        //         $productColor = $product->colors()->where('color_id', $colorId)->first();
        
        //         // Check if the product already has this color
        //         if (!$productColor) {
        //             $productColor = new ProductColor();
        //             $productColor->product_id = $product->id;
        //             $productColor->color_id = $colorId;
        //         }
        
        //         // Check if a new image is uploaded for this color
        //         if ($request->hasFile('image.' . $key)) {
        //             if ($productColor->image && file_exists(public_path($productColor->image))) {
        //                 unlink(public_path($productColor->image));
        //             }
                    
        //             // Upload the new image
        //             $colorImage = $request->file('image.' . $key);
        //             $randomName = mt_rand(10000000, 99999999) . '.' . $colorImage->getClientOriginalExtension();
        //             $colorImage->move(public_path('images/products'), $randomName);
        //             $productColor->image = '/images/products/' . $randomName;
        //         }
    
        //         $productColor->created_by = auth()->user()->id;
        //         $productColor->save();
        //     }
        // }

        return response()->json(['message' => 'Product updated successfully!', 'product' => $product], 200);
    }

    public function showProductPrices($productId)
    {
        $product = Product::findOrFail($productId);
        $prices = $product->prices;
        return view('admin.product.prices', compact('product', 'prices'));
    }

    public function storePrice(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'min_quantity' => 'required|integer',
            'max_quantity' => 'required|integer',
            'price' => 'required|numeric',
        ]);

        $data = new ProductPrice;
        $data->product_id = $request->product_id;
        $data->min_quantity = $request->min_quantity;
        $data->max_quantity = $request->max_quantity;
        $data->price = $request->price;
        $data->status = $request->status ?? 1;
        $data->created_by = auth()->id();

        if ($data->save()) {
            return response()->json(['status' => 300, 'message' => 'Price created successfully.']);
        } else {
            return response()->json(['status' => 303, 'message' => 'Server Error!']);
        }
    }

    public function priceEdit($id)
    {
        $price = ProductPrice::findOrFail($id);
        return response()->json($price);
    }

    public function updatePrice(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'min_quantity' => 'required|integer',
            'max_quantity' => 'required|integer',
            'price' => 'required|numeric',
            'priceId' => 'required|exists:product_prices,id',
        ]);

        $price = ProductPrice::find($request->priceId);
        $price->min_quantity = $request->min_quantity;
        $price->max_quantity = $request->max_quantity;
        $price->price = $request->price;
        $price->status = $request->status ?? 1;
        $price->updated_by = auth()->id();

        if ($price->save()) {
            return response()->json(['status' => 300, 'message' => 'Price updated successfully.']);
        } else {
            return response()->json(['status' => 303, 'message' => 'Failed to update price.']);
        }
    }

    public function deletePrice($id)
    {
        $price = ProductPrice::find($id);
        
        if (!$price) {
            return response()->json(['success' => false, 'message' => 'Price not found.'], 404);
        }

        if ($price->delete()) {
            return response()->json(['success' => true, 'message' => 'Price deleted successfully.']);
        } else {
            return response()->json(['success' => false, 'message' => 'Failed to delete price.'], 500);
        }
    }

    public function updatePriceStatus(Request $request)
    {
        $price = ProductPrice::find($request->price_id);
        if (!$price) {
            return response()->json(['status' => 404, 'message' => 'Price not found']);
        }

        $price->status = $request->status;
        $price->save();

        return response()->json(['status' => 200, 'message' => 'Price status updated successfully']);
    }

    public function productReviews($productId)
    {
        $product = Product::with('reviews')->findOrFail($productId);
        return view('admin.product.reviews', compact('product'));
    }

    public function changeReviewStatus(Request $request)
    {
        $review = ProductReview::findOrFail($request->review_id);
        $review->is_approved = $request->is_approved;
        $review->updated_by = auth()->id();
        $review->save();

        return response()->json(['success' => true]);
    }

}
