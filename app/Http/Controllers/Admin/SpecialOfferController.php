<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\SpecialOffer;
use App\Models\SpecialOfferDetails;
use Illuminate\Support\Str;
use App\Models\CompanyDetails;

class SpecialOfferController extends Controller
{
    public function createSpecialOffer()
    {
        $products = Product::whereDoesntHave('flashSellDetails')
                    ->whereDoesntHave('specialOfferDetails')
                    ->orderBy('id', 'DESC')
                    ->get();

        foreach ($products as $product) {
            $sellingPrice = $product->stockhistory()
                                        ->where('available_qty', '>', 0)
                                        ->orderBy('id', 'asc')
                                        ->value('selling_price');

            $product->price = $sellingPrice ?? $product->price;
        }

        return view('admin.special_offer.create',compact('products'));
    }

    public function specialOfferStore(Request $request)
    {
        $request->validate([
            'offer_name' => 'required|string',
            'offer_title' => 'required|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date',
            'offer_description' => 'required|string',
            'offer_image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'products' => 'required|array',
        ]);

        $offerImage = $request->file('offer_image');
        $imageName = mt_rand(10000000, 99999999) . '.' . $offerImage->getClientOriginalExtension();
        $destinationPath = public_path('images/special_offer');
        $offerImage->move($destinationPath, $imageName);

        $specialOffer = new SpecialOffer();
        $specialOffer->offer_name = $request->input('offer_name');
        $specialOffer->offer_title = $request->input('offer_title');
        $specialOffer->slug = Str::slug($request->input('offer_title'));
        $specialOffer->start_date = $request->input('start_date');
        $specialOffer->end_date = $request->input('end_date');
        $specialOffer->offer_description = $request->input('offer_description');
        $specialOffer->offer_image = $imageName;
        $specialOffer->save();

        $products = $request->input('products');
        foreach ($products as $productJson) {
            $product = json_decode($productJson, true);
            $specialOfferDetail = new SpecialOfferDetails();
            $specialOfferDetail->special_offer_id = $specialOffer->id;
            $specialOfferDetail->product_id = $product['product_id'];
            $specialOfferDetail->quantity = $product['quantity'];
            $specialOfferDetail->old_price = $product['old_price'];
            $specialOfferDetail->offer_price = $product['offer_price'];
            $specialOfferDetail->created_by = auth()->user()->id;
            $specialOfferDetail->save();
        }

        return response()->json(['message' => 'Special offer created successfully'], 200);
    }

    public function specialOffers()
    {
        $specialOffers = SpecialOffer::with('specialOfferDetails')
        ->orderBy('id', 'DESC')
        ->get();
        return view('admin.special_offer.index',compact('specialOffers'));
    }

    public function getOfferDetails($id)
    {
        $specialOffer = SpecialOffer::with('specialOfferDetails.product')->findOrFail($id);
        return response()->json($specialOffer);
    }

    public function edit($id)
    {
        $specialOffer = SpecialOffer::with('specialOfferDetails')->findOrFail($id); 
        $products = Product::orderby('id','DESC')->get();
        foreach ($products as $product) {
            $sellingPrice = $product->stockhistory()
                                        ->where('available_qty', '>', 0)
                                        ->orderBy('id', 'asc')
                                        ->value('selling_price');

            $product->price = $sellingPrice ?? $product->price;
        }
        return view('admin.special_offer.edit', compact('specialOffer', 'products'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'offer_id' => 'required|exists:special_offers,id',
            'offer_name' => 'required|string',
            'offer_title' => 'required|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date',
            'offer_description' => 'required|string',
            'offer_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'products' => 'required|array',
        ]);

        $offer = SpecialOffer::findOrFail($request->input('offer_id'));

        if ($request->hasFile('offer_image')) {
            $offerImage = $request->file('offer_image');
            $imageName = mt_rand(10000000, 99999999) . '.' . $offerImage->getClientOriginalExtension();
            $destinationPath = public_path('images/special_offer');
            $offerImage->move($destinationPath, $imageName);

            $oldImagePath = public_path('images/special_offer/' . $offer->offer_image);
            if (file_exists($oldImagePath)) {
                unlink($oldImagePath);
            }

            $offer->offer_image = $imageName;
        }

        $offer->offer_name = $request->input('offer_name');
        $offer->offer_title = $request->input('offer_title');
        $offer->slug = Str::slug($request->input('offer_title'));
        $offer->start_date = $request->input('start_date');
        $offer->end_date = $request->input('end_date');
        $offer->offer_description = $request->input('offer_description');
        $offer->save();

        SpecialOfferDetails::where('special_offer_id', $offer->id)->delete();

        foreach ($request->input('products') as $productJson) {
            $product = json_decode($productJson, true);
            $detail = new SpecialOfferDetails();
            $detail->special_offer_id = $offer->id;
            $detail->product_id = $product['product_id'];
            $detail->quantity = $product['quantity'];
            $detail->old_price = $product['old_price'];
            $detail->offer_price = $product['offer_price'];
            $detail->created_by = auth()->user()->id;
            $detail->save();
        }

        return response()->json(['message' => 'Special offer updated successfully'], 200);
    }

    public function show($slug)
    {
        $specialOffer = SpecialOffer::with('specialOfferDetails.product')->where('slug', $slug)->firstOrFail();
        $company = CompanyDetails::select('company_name')
                             ->first();
        $title = $company->company_name . ' - ' . $specialOffer->offer_name;
        return view('frontend.special_offer', compact('specialOffer', 'title'));
    }

    public function destroy($id)
    {
        $specialOffer = SpecialOffer::findOrFail($id);
        if ($specialOffer->offer_image) {
            $imagePath = public_path('images/special_offer/' . $specialOffer->offer_image);
            if (file_exists($imagePath)) {
                unlink($imagePath);
            }
        }
        $specialOffer->delete();

        return response()->json(['message' => 'Flash sell deleted successfully.'], 200);
    }
}
