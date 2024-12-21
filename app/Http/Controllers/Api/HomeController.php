<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Slider;
use App\Models\Supplier;
use App\Models\Ad;
use App\Models\SectionStatus;
use App\Models\BundleProduct;
use App\Models\BuyOneGetOne;
use App\Models\Product;
use App\Models\FlashSell;
use App\Models\SpecialOffer;
use App\Models\CompanyDetails;
use App\Models\Contact;
use App\Models\SubCategory;
use App\Models\SpecialOfferDetails;
use App\Models\FlashSellDetails;
use App\Models\RelatedProduct;
use App\Models\SupplierStock;

class HomeController extends Controller
{
    public function index()
    {
        $currency = CompanyDetails::value('currency');

        $specialOffers = SpecialOffer::select('offer_image', 'offer_name', 'offer_title', 'slug')
            ->where('status', 1)
            ->whereDate('start_date', '<=', now())
            ->whereDate('end_date', '>=', now())
            ->latest()
            ->get();

        $flashSells = FlashSell::select('flash_sell_image', 'flash_sell_name', 'flash_sell_title', 'slug')
            ->where('status', 1)
            ->whereDate('start_date', '<=', now())
            ->whereDate('end_date', '>=', now())
            ->latest()
            ->get();

        $featuredProducts = Product::where('status', 1)
            ->where('is_featured', 1)
            ->whereDoesntHave('specialOfferDetails')
            ->whereDoesntHave('flashSellDetails')
            ->with('stock')
            ->orderBy('id', 'desc')
            ->select('id', 'name', 'feature_image', 'price', 'slug')
            ->get();

        $trendingProducts = Product::where('status', 1)
            ->where('is_trending', 1)
            ->orderByDesc('id')
            ->whereDoesntHave('specialOfferDetails')
            ->whereDoesntHave('flashSellDetails')
            ->with('stock')
            ->select('id', 'name', 'feature_image', 'slug', 'price')
            ->get();

        $recentProducts = Product::where('status', 1)
            ->where('is_recent', 1)
            ->orderByDesc('id')
            ->whereDoesntHave('specialOfferDetails')
            ->whereDoesntHave('flashSellDetails')
            ->with('stock')
            ->select('id', 'name', 'feature_image', 'price', 'slug')
            ->get();

        $popularProducts = Product::where('status', 1)
            ->where('is_popular', 1)
            ->orderBy('watch', 'desc')
            ->whereDoesntHave('specialOfferDetails')
            ->whereDoesntHave('flashSellDetails')
            ->with('stock')
            ->select('id', 'name', 'feature_image', 'price', 'slug', 'watch')
            ->get();

        $categoryProducts = Category::where('status', 1)
            ->with(['products' => function($query) {
                $query->select('id', 'name', 'feature_image', 'price', 'slug', 'category_id')
                    ->where('status', 1)
                    ->whereDoesntHave('specialOfferDetails')
                    ->whereDoesntHave('flashSellDetails')
                    ->orderBy('id', 'desc');
                }])
                ->select('id', 'name')
                ->get();


        $buyOneGetOneProducts = BuyOneGetOne::where('status', 1)
            ->with(['product' => function($query) {
                $query->select('id', 'name', 'feature_image', 'price', 'slug');
            }])
            ->get()
            ->map(function($bogo) {
                $bogo->get_products = Product::whereIn('id', json_decode($bogo->get_product_ids))
                    ->select('id', 'name', 'feature_image', 'price', 'slug')
                    ->get();
                return $bogo;
            });

        $bundleProducts = BundleProduct::all();
            foreach ($bundleProducts as $bundle) {
                $bundle->product_ids = json_decode($bundle->product_ids);
            }

        $section_status = SectionStatus::first();
        $advertisements = Ad::where('status', 1)->select('type', 'link', 'image')->get();

        $suppliers = Supplier::orderBy('id', 'desc')
                        ->select('id', 'name', 'image', 'slug')
                        ->get();

         $sliders = Slider::orderBy('id', 'desc')
                        ->select('title', 'sub_title', 'image')
                        ->get();

        $categories = Category::where('status', 1)->select('name', 'image', 'slug')->orderBy('id', 'desc')->take(2)->get();

        return response()->json([
            'currency' => $currency,
            'specialOffers' => $specialOffers,
            'flashSells' => $flashSells,
            'featuredProducts' => $featuredProducts,
            'trendingProducts' => $trendingProducts,
            'recentProducts' => $recentProducts,
            'popularProducts' => $popularProducts,
            'categoryProducts' => $categoryProducts,
            'buyOneGetOneProducts' => $buyOneGetOneProducts,
            'bundleProducts' => $bundleProducts,
            'section_status' => $section_status,
            'advertisements' => $advertisements,
            'suppliers' => $suppliers,
            'sliders' => $sliders,
            'categories' => $categories,
        ]);

    }

    public function shop()
    {
        $currency = CompanyDetails::value('currency');

        $categories = Category::where('status', 1)
            ->orderBy('id', 'desc')
            ->select('id', 'name')
            ->get();

        $products = Product::where('status', 1)
            ->orderBy('id', 'desc')
            ->whereDoesntHave('specialOfferDetails')
            ->whereDoesntHave('flashSellDetails')
            ->with('stock')
            ->select('id', 'name', 'feature_image', 'price', 'slug')
            ->get();

        return response()->json([
            'currency' => $currency,
            'categories' => $categories,
            'products' => $products,
        ]);
    }

    public function aboutUs()
    {
        $companyDetails = CompanyDetails::select('about_us')->first();
        return response()->json([
            'companyDetails' => $companyDetails
        ]);
    }

    public function contact()
    {
        $companyDetails = CompanyDetails::select('google_map', 'address1', 'email1', 'phone1')->first();
        return response()->json([
            'companyDetails' => $companyDetails
        ]);
    }

    public function showProduct($slug, $offerId = null)
    {
        $product = Product::where('slug', $slug)->with('images')->firstOrFail();
        $supplierPrice = null;

        $product->watch = $product->watch + 1;
        $product->save();
        $specialOffer = null;
        $flashSell = null;
        $offerPrice = null;
        $flashSellPrice = null;
        $oldOfferPrice = null;
        $OldFlashSellPrice = null;

        if ($offerId == 1) {
            $specialOffer = SpecialOfferDetails::where('product_id', $product->id)
                ->whereHas('specialOffer', function ($query) {
                    $query->whereDate('start_date', '<=', now())
                        ->whereDate('end_date', '>=', now());
                })
                ->first();
            $offerPrice = $specialOffer ? $specialOffer->offer_price : null;
            $oldOfferPrice = $specialOffer ? $specialOffer->old_price : null;
        } elseif ($offerId == 2) {
            $flashSell = FlashSellDetails::where('product_id', $product->id)
                ->whereHas('flashsell', function ($query) {
                    $query->whereDate('start_date', '<=', now())
                        ->whereDate('end_date', '>=', now());
                })
                ->first();
            
            $flashSellPrice = $flashSell ? $flashSell->flash_sell_price : null;
            $OldFlashSellPrice = $flashSell ? $flashSell->old_price : null;
        }

        $regularPrice = $product->price;

        $company_name = CompanyDetails::value('company_name');
        $title = $company_name . ' - ' . $product->name;
        $currency = CompanyDetails::value('currency');

        $relatedProducts = RelatedProduct::where('product_id', $product->id)
            ->where('status', 1)
            ->first();

        if ($relatedProducts && $relatedProducts->related_product_ids) {
            $relatedProductIds = json_decode($relatedProducts->related_product_ids, true);

            $relatedProducts = Product::whereIn('id', $relatedProductIds)
                ->where('id', '!=', $product->id)
                ->select('id', 'name', 'feature_image', 'price', 'slug')
                ->orderByDesc('created_at')
                ->get();
        } else {
            $relatedProducts = Product::where('category_id', $product->category_id)
                ->whereDoesntHave('specialOfferDetails')
                ->whereDoesntHave('flashSellDetails')
                ->where('id', '!=', $product->id)
                ->select('id', 'name', 'feature_image', 'price', 'slug')
                ->orderByDesc('created_at')
                ->take(5)
                ->get();
        }

        return response()->json([
            'product' => $product,
            'relatedProducts' => $relatedProducts,
            'regularPrice' => $regularPrice,
            'offerPrice' => $offerPrice,
            'flashSellPrice' => $flashSellPrice,
            'offerId' => $offerId,
            'currency' => $currency,
            'oldOfferPrice' => $oldOfferPrice,
            'OldFlashSellPrice' => $OldFlashSellPrice,
        ]);
    }

    public function bogoShowProduct($slug)
    {
        $product = Product::where('slug', $slug)->with('images')->firstOrFail();

        $product->watch = $product->watch + 1;
        $product->save();

        $regularPrice = $product->price;

        $bogo = BuyOneGetOne::with('images')->where('product_id', $product->id)
            ->where('status', 1)
            ->first();

        if ($bogo) {
            $regularPrice = $bogo->price;

            $getProductIds = json_decode($bogo->get_product_ids, true);
            $bogoProducts = Product::whereIn('id', $getProductIds)
                ->select('id', 'name', 'feature_image', 'price', 'slug')
                ->get();
        } else {
            $bogoProducts = collect();
        }

        $currency = CompanyDetails::value('currency');

        $quantity = $bogo ? $bogo->quantity : null;

        $response = [
            'product' => $product,
            'regularPrice' => $regularPrice,
            'currency' => $currency,
            'quantity' => $quantity,
            'bogo' => $bogo,
            'bogoProducts' => $bogoProducts,
        ];

        return response()->json($response, 200);
    }

    public function bundleSingleProduct($slug)
    {
        $bundle = BundleProduct::with('images')
            ->where('slug', $slug)
            ->where('status', 1)
            ->firstOrFail();

        $productIds = json_decode($bundle->product_ids, true);
        $bundleProducts = Product::whereIn('id', $productIds)
            ->select('id', 'name', 'feature_image', 'price', 'slug')
            ->get();

        $currency = CompanyDetails::value('currency');

        $response = [
            'bundle' => $bundle,
            'currency' => $currency,
            'bundleProducts' => $bundleProducts,
        ];

        return response()->json($response, 200);
    }

    public function showSupplierProduct($slug, $supplierId = null)
    {
        $product = Product::where('slug', $slug)->with('images')->firstOrFail();

        $regularPrice = $product->price;
        $stockQuantity = null;
        $stockDescription = null;

        if ($supplierId) {
            $stock = SupplierStock::where('product_id', $product->id)
                ->where('supplier_id', $supplierId)
                ->first();

            if ($stock) {
                $regularPrice = $stock->price;
                $stockQuantity = $stock->quantity;
                $stockDescription = $stock->description;
            }
        }

        $product->watch = $product->watch + 1;
        $product->save();

        $currency = CompanyDetails::value('currency');

        $response = [
            'product' => $product,
            'title' => $product->title,
            'regularPrice' => $regularPrice,
            'currency' => $currency,
            'stockQuantity' => $stockQuantity,
            'stockDescription' => $stockDescription,
        ];

        return response()->json($response, 200);
    }

    public function contactStore(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'subject' => 'nullable|string|max:255',
            'message' => 'required|string',
        ]);

        $contact = Contact::create([
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'subject' => $validatedData['subject'],
            'message' => $validatedData['message'],
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Your message has been sent successfully!',
            'data' => $contact
        ], 201);
    }

    public function showCategoryProducts($slug)
    {
        $currency = CompanyDetails::value('currency');
        $category = Category::where('slug', $slug)
                        ->with(['products:id,category_id,name,feature_image,price,slug'])
                        ->firstOrFail();

        return response()->json([
            'currency' => $currency,
            'category' => $category,
        ], 200);
    }

    public function showSubCategoryProducts($slug)
    {
        $currency = CompanyDetails::value('currency');
        $sub_category = SubCategory::where('slug', $slug)
                        ->with(['products:id,sub_category_id,name,feature_image,price,slug'])
                        ->firstOrFail();

        return response()->json([
            'currency' => $currency,
            'sub_category' => $sub_category,
        ], 200);
    }

    public function specialOffer($slug)
    {
        $specialOffer = SpecialOffer::with('specialOfferDetails.product')->where('slug', $slug)->firstOrFail();
        return response()->json($specialOffer, 200);
    }

    public function flashSell($slug)
    {
        $flashSell = FlashSell::with('FlashSellDetails.product')->where('slug', $slug)->firstOrFail();
        return response()->json($flashSell, 200);
    }


    public function getAllAds()
    {
        $ads = Ad::where('status', 1)->latest()->get();
        return response()->json($ads, 200);
    }

    public function getAllSliders()
    {
        $sliders = Slider::latest()->get();
        return response()->json($sliders, 200);
    }

    public function getFeaturedProducts()
    {
        $featuredProducts = Product::where('status', 1)
            ->where('is_featured', 1)
            ->whereDoesntHave('specialOfferDetails')
            ->whereDoesntHave('flashSellDetails')
            ->with('stock')
            ->orderBy('id', 'desc')
            ->select('id', 'name', 'feature_image', 'price', 'slug')
            ->get();

        return response()->json($featuredProducts, 200);
    }

    public function getTrendingProducts()
    {
        $trendingProducts = Product::where('status', 1)
            ->where('is_trending', 1)
            ->orderByDesc('id')
            ->whereDoesntHave('specialOfferDetails')
            ->whereDoesntHave('flashSellDetails')
            ->with('stock')
            ->select('id', 'name', 'feature_image', 'slug', 'price')
            ->get();

        return response()->json($trendingProducts, 200);
    }

    public function getRecentProducts()
    {
        $recentProducts = Product::where('status', 1)
            ->where('is_recent', 1)
            ->orderByDesc('id')
            ->whereDoesntHave('specialOfferDetails')
            ->whereDoesntHave('flashSellDetails')
            ->with('stock')
            ->select('id', 'name', 'feature_image', 'price', 'slug')
            ->get();

        return response()->json($recentProducts, 200);
    }

    public function getPopularProducts()
    {
        $popularProducts = Product::where('status', 1)
            ->where('is_popular', 1)
            ->orderBy('watch', 'desc')
            ->whereDoesntHave('specialOfferDetails')
            ->whereDoesntHave('flashSellDetails')
            ->with('stock')
            ->select('id', 'name', 'feature_image', 'price', 'slug', 'watch')
            ->get();

        return response()->json($popularProducts, 200);
    }

    public function getAllCategoryProducts()
    {
        $categories = Category::where('status', 1)
            ->with(['products' => function($query) {
                $query->select('id', 'name', 'feature_image', 'price', 'slug', 'category_id')
                    ->where('status', 1)
                    ->whereDoesntHave('specialOfferDetails')
                    ->whereDoesntHave('flashSellDetails')
                    ->orderBy('id', 'desc');
            }])
            ->select('id', 'name')
            ->get();

        return response()->json($categories, 200);
    }

    

}
