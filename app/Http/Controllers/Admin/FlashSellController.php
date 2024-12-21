<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\FlashSell;
use Illuminate\Support\Str;
use App\Models\FlashSellDetails;
use App\Models\CompanyDetails;

class FlashSellController extends Controller
{
    public function createFlashSell()
    {
        $products = Product::whereDoesntHave('specialOfferDetails')
                    ->whereDoesntHave('flashSellDetails')
                    ->orderBy('id', 'DESC')
                    ->get();

        foreach ($products as $product) {
            $sellingPrice = $product->stockhistory()
                                        ->where('available_qty', '>', 0)
                                        ->orderBy('id', 'asc')
                                        ->value('selling_price');

            $product->price = $sellingPrice ?? $product->price;
        }

        return view('admin.flash_sell.create',compact('products'));
    }

    public function flashSellStore(Request $request)
    {
        $request->validate([
            'flash_sell_name' => 'required|string',
            'flash_sell_title' => 'required|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date',
            'flash_sell_description' => 'required|string',
            'flash_sell_image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'products' => 'required|array',
        ]);

        $flashSellImage = $request->file('flash_sell_image');
        $imageName = mt_rand(10000000, 99999999) . '.' . $flashSellImage->getClientOriginalExtension();
        $destinationPath = public_path('images/flash_sell');
        $flashSellImage->move($destinationPath, $imageName);

        $flashSell = new FlashSell();
        $flashSell->flash_sell_name = $request->input('flash_sell_name');
        $flashSell->flash_sell_title = $request->input('flash_sell_title');
        $flashSell->slug = Str::slug($request->input('flash_sell_title'));
        $flashSell->start_date = $request->input('start_date');
        $flashSell->end_date = $request->input('end_date');
        $flashSell->flash_sell_description = $request->input('flash_sell_description');
        $flashSell->flash_sell_image = $imageName;
        $flashSell->save();

        $products = $request->input('products');
        foreach ($products as $productJson) {
            $product = json_decode($productJson, true);
            $flashSellDetail = new FlashSellDetails();
            $flashSellDetail->flash_sell_id = $flashSell->id;
            $flashSellDetail->product_id = $product['product_id'];
            $flashSellDetail->quantity = $product['quantity'];
            $flashSellDetail->old_price = $product['old_price'];
            $flashSellDetail->flash_sell_price = $product['flash_price'];
            $flashSellDetail->created_by = auth()->user()->id;
            $flashSellDetail->save();
        }

        return response()->json(['message' => 'Flash sell created successfully'], 200);
    }

    public function flashSells()
    {
        $flashSells = FlashSell::with('FlashSellDetails')
        ->orderBy('id', 'DESC')
        ->get();
        return view('admin.flash_sell.index',compact('flashSells'));
    }

    public function getFlashSellDetails($id)
    {
        $flashSell = FlashSell::with('FlashSellDetails.product')->findOrFail($id);
        return response()->json($flashSell);
    }

    public function edit($id)
    {
        $flashSell = FlashSell::with('FlashSellDetails')->findOrFail($id); 
        $products = Product::orderby('id','DESC')->get();
        foreach ($products as $product) {
            $sellingPrice = $product->stockhistory()
                                        ->where('available_qty', '>', 0)
                                        ->orderBy('id', 'asc')
                                        ->value('selling_price');

            $product->price = $sellingPrice ?? $product->price;
        }
        return view('admin.flash_sell.edit', compact('flashSell', 'products'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'flash_sell_id' => 'required|exists:flash_sells,id',
            'flash_sell_name' => 'required|string',
            'flash_sell_title' => 'required|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date',
            'flash_sell_description' => 'required|string',
            'flash_sell_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'products' => 'required|array',
        ]);

        $flashSell = FlashSell::findOrFail($request->input('flash_sell_id'));

        if ($request->hasFile('flash_sell_image')) {
            $flashSellImage = $request->file('flash_sell_image');
            $imageName = mt_rand(10000000, 99999999) . '.' . $flashSellImage->getClientOriginalExtension();
            $destinationPath = public_path('images/flash_sell');
            $flashSellImage->move($destinationPath, $imageName);

            $oldImagePath = public_path('images/flash_sell/' . $flashSell->flash_sell_image);
            if (file_exists($oldImagePath)) {
                unlink($oldImagePath);
            }

            $flashSell->flash_sell_image = $imageName;
        }

        $flashSell->flash_sell_name = $request->input('flash_sell_name');
        $flashSell->flash_sell_title = $request->input('flash_sell_title');
        $flashSell->slug = Str::slug($request->input('flash_sell_title'));
        $flashSell->start_date = $request->input('start_date');
        $flashSell->end_date = $request->input('end_date');
        $flashSell->flash_sell_description = $request->input('flash_sell_description');
        $flashSell->save();

        FlashSellDetails::where('flash_sell_id', $flashSell->id)->delete();

        foreach ($request->input('products') as $productJson) {
            $product = json_decode($productJson, true);
            $flashSellDetail = new FlashSellDetails();
            $flashSellDetail->flash_sell_id = $flashSell->id;
            $flashSellDetail->product_id = $product['product_id'];
            $flashSellDetail->quantity = $product['quantity'];
            $flashSellDetail->old_price = $product['old_price'];
            $flashSellDetail->flash_sell_price = $product['flash_sell_price'];
            $flashSellDetail->created_by = auth()->user()->id;
            $flashSellDetail->save();
        }

        return response()->json(['message' => 'Flash sell updated successfully'], 200);
    }

    public function show($slug)
    {
        $flashSell = FlashSell::with('FlashSellDetails.product')->where('slug', $slug)->firstOrFail();
        $company = CompanyDetails::select('company_name')
                             ->first();
        $title = $company->company_name . ' - ' . $flashSell->flash_sell_name;
        return view('frontend.flash_sell', compact('flashSell', 'title'));
    }

    public function destroy($id)
    {
        $flashSell = FlashSell::findOrFail($id);
        if ($flashSell->flash_sell_image) {
            $imagePath = public_path('images/flash_sell/' . $flashSell->flash_sell_image);
            if (file_exists($imagePath)) {
                unlink($imagePath);
            }
        }
        $flashSell->delete();

        return response()->json(['message' => 'Flash sell deleted successfully.'], 200);
    }

}
