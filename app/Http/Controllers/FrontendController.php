<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Category;
use App\Models\Product;
use session;
use App\Models\CompanyDetails;
use App\Models\Contact;
use App\Models\SpecialOfferDetails;
use App\Models\FlashSell;
use App\Models\FlashSellDetails;
use App\Models\Coupon;
use App\Models\SubCategory;
use App\Models\Stock;
use App\Models\SpecialOffer;
use App\Models\SectionStatus;
use App\Models\Ad;
use App\Models\Supplier;
use App\Models\Slider;
use App\Models\SupplierStock;
use App\Models\RelatedProduct;
use App\Models\BuyOneGetOne;
use App\Models\BundleProduct;
use App\Models\Campaign;
use App\Models\CampaignRequest;
use App\Models\CampaignRequestProduct;
use App\Models\Brand;
use App\Models\Color;
use App\Models\CouponUsage;
use App\Models\StockHistory;
use App\Models\Size;
use App\Models\ProductReview;

class FrontendController extends Controller
{
    // public function index()
    // {
    //     $currency = CompanyDetails::value('currency');
    //     $specialOffers = SpecialOffer::select('offer_image', 'offer_name', 'offer_title', 'slug')
    //         ->where('status', 1)
    //         ->whereDate('start_date', '<=', now())
    //         ->whereDate('end_date', '>=', now())
    //         ->latest()
    //         ->get();
    //     $flashSells = FlashSell::select('flash_sell_image', 'flash_sell_name', 'flash_sell_title', 'slug')
    //         ->where('status', 1)
    //         ->whereDate('start_date', '<=', now())
    //         ->whereDate('end_date', '>=', now())
    //         ->latest()
    //         ->get();

    //     $campaigns = Campaign::select('banner_image', 'title', 'slug')
    //         ->whereDate('start_date', '<=', now())
    //         ->whereDate('end_date', '>=', now())
    //         ->latest()
    //         ->get();
    //     $trendingProducts = Product::where('status', 1)
    //         ->where('is_trending', 1)
    //         ->orderByDesc('id')
    //         ->whereDoesntHave('specialOfferDetails')
    //         ->whereDoesntHave('flashSellDetails')
    //         ->with('stock')
    //         ->select('id', 'name', 'feature_image', 'slug', 'price')
    //         ->take(12)
    //         ->get();

    //     $mostViewedProducts = Product::where('status', 1)
    //         ->where('is_recent', 1)
    //         ->orderByDesc('watch')
    //         ->whereDoesntHave('specialOfferDetails')
    //         ->whereDoesntHave('flashSellDetails')
    //         ->with('stock')
    //         ->select('id', 'name', 'feature_image', 'price', 'slug')
    //         ->take(12)
    //         ->get();

    //     $recentProducts = Product::where('status', 1)
    //         ->where('is_recent', 1)
    //         ->orderByDesc('id')
    //         ->whereDoesntHave('specialOfferDetails')
    //         ->whereDoesntHave('flashSellDetails')
    //         ->with('stock')
    //         ->select('id', 'name', 'feature_image', 'price', 'slug')
    //         ->take(12)
    //         ->get();
    //     $buyOneGetOneProducts = BuyOneGetOne::where('status', 1)
    //         ->with(['product' => function($query) {
    //             $query->select('id', 'name', 'feature_image', 'price', 'slug');
    //         }])
    //         ->get()
    //         ->map(function($bogo) {
    //             $bogo->get_products_count = Product::whereIn('id', json_decode($bogo->get_product_ids))->count();
    //             return $bogo;
    //         });

    //     $bundleProducts = BundleProduct::select('id', 'name', 'feature_image', 'price', 'slug', 'product_ids')
    //         ->get()
    //         ->map(function($bundle) {
    //             $bundle->product_ids_count = json_decode($bundle->product_ids, true) ? count(json_decode($bundle->product_ids, true)) : 0;
    //             return $bundle;
    //         });

    //     $section_status = SectionStatus::first();
    //     $advertisements = Ad::where('status', 1)->select('type', 'link', 'image')->get();

    //     $suppliers = Supplier::where('status', 1)
    //                     ->orderBy('id', 'desc')
    //                     ->select('id', 'name', 'image', 'slug')
    //                     ->get();

    //     $sliders = Slider::orderBy('id', 'asc')
    //              ->where('status', 1)
    //              ->select('title', 'sub_title', 'image', 'link')
    //              ->get();

    //     $categories = Category::where('status', 1)
    //         ->with(['products' => function ($query) {
    //             $query->select('id', 'category_id', 'name', 'price', 'slug', 'feature_image', 'watch')
    //                 ->orderBy('watch', 'desc');
    //         }])
    //         ->select('id', 'name', 'image', 'slug')
    //         ->orderBy('id', 'asc')
    //         ->get()
    //         ->each(function ($category) {
    //             $category->setRelation('products', $category->products->take(6));
    //         });

    //     $companyDesign = CompanyDetails::value('design');

    //     if (in_array($companyDesign, ['2', '3', '4'])) {
    //         return view('frontend.index2', compact('specialOffers', 'flashSells', 'trendingProducts', 'currency', 'recentProducts', 'buyOneGetOneProducts', 'bundleProducts', 'section_status', 'advertisements', 'suppliers', 'sliders', 'categories', 'campaigns', 'mostViewedProducts'));
    //     } elseif ($companyDesign == '5') {
    //         return view('frontend.index5', compact('specialOffers', 'flashSells', 'trendingProducts', 'currency', 'recentProducts', 'buyOneGetOneProducts', 'bundleProducts', 'section_status', 'advertisements', 'suppliers', 'sliders', 'categories', 'campaigns', 'mostViewedProducts'));
    //     } else {
    //         return view('frontend.index', compact('specialOffers', 'flashSells', 'trendingProducts', 'currency', 'recentProducts', 'buyOneGetOneProducts', 'bundleProducts', 'section_status', 'advertisements', 'suppliers', 'sliders', 'categories', 'campaigns', 'mostViewedProducts'));
    //     }

    // }

    public function login()
    {  
        if (auth()->user()) {
            return redirect()->route('admin.dashboard');
        }else {
            return view('auth.login');
        }
    }

    public function getCategoryProducts(Request $request)
    {
        $categoryId = $request->input('category_id');
        $page = $request->input('page', 1);
        $perPage = 6;

        $query = Product::where('category_id', $categoryId)
                        ->where('status', 1)
                        ->whereDoesntHave('specialOfferDetails')
                        ->whereDoesntHave('flashSellDetails')
                        ->select('id', 'name', 'feature_image', 'price', 'slug')
                        ->orderBy('id', 'desc');

        $shownProducts = $request->input('shown_products', []);
        if (!empty($shownProducts)) {
            $query->whereNotIn('id', $shownProducts);
        }

        $products = $query->paginate($perPage, ['*'], 'page', $page);

        return response()->json($products);
    }

    public function showCategoryProducts($slug)
    {
        $currency = CompanyDetails::value('currency');

        $category = Category::where('slug', $slug)->firstOrFail();

        $products = Product::where('category_id', $category->id)
                            ->select('id', 'category_id', 'name', 'feature_image', 'price', 'slug')
                            ->paginate(20);
        
        $company = CompanyDetails::select('company_name')->first();
        $title = $company->company_name . ' - ' . $category->name;
        
        return view('frontend.category_products', compact('category', 'products', 'title', 'currency'));
    }    

    public function showSubCategoryProducts($slug)
    {
        $currency = CompanyDetails::value('currency');

        $sub_category = SubCategory::where('slug', $slug)->firstOrFail();

        $products = Product::where('sub_category_id', $sub_category->id)
                            ->select('id', 'sub_category_id', 'name', 'feature_image', 'price', 'slug')
                            ->paginate(20);

        $company = CompanyDetails::select('company_name')->first();
        $title = $company->company_name . ' - ' . $sub_category->name;

        return view('frontend.sub_category_products', compact('sub_category', 'products', 'title', 'currency'));
    }

    public function showProduct($slug, $offerId = null)
    {
        $product = Product::where('slug', $slug)->with(['colors.color', 'stockhistory', 'stock', 'reviews'])->firstOrFail();
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

        return view('frontend.product.single_product', compact('product', 'relatedProducts', 'title', 'regularPrice', 'offerPrice', 'flashSellPrice', 'offerId', 'currency', 'oldOfferPrice', 'OldFlashSellPrice'));
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

        $company_name = CompanyDetails::value('company_name');
        $title = $company_name . ' - ' . $product->name;
        $currency = CompanyDetails::value('currency');
        $quantity = $bogo ? $bogo->quantity : null;

        return view('frontend.product.bogo_single_product', compact('product', 'title', 'regularPrice', 'currency', 'quantity', 'bogo', 'bogoProducts'));
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

        $company_name = CompanyDetails::value('company_name');
        $title = $company_name . ' - ' . $bundle->name;
        $currency = CompanyDetails::value('currency');

        return view('frontend.product.bundle_single_product', compact('bundle', 'title', 'currency', 'bundleProducts'));
    }

    public function showSupplierProduct($slug, $supplierId = null)
    {
        $product = Product::where('slug', $slug)->with('images')->firstOrFail();
        $regularPrice = null;

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

        $company_name = CompanyDetails::value('company_name');
        $title = $company_name . ' - ' . $product->name;
        $currency = CompanyDetails::value('currency');

        return view('frontend.product.supplier_single_product', compact('product', 'title', 'regularPrice', 'currency', 'stockQuantity', 'stockDescription', 'supplierId'));
    }

    public function storeWishlist(Request $request)
    {
        $request->session()->put('wishlist', $request->input('wishlist'));
        return response()->json(['success' => true]);
    }

    public function showWishlist(Request $request)
    {
        $wishlistJson = $request->session()->get('wishlist', '[]');
        $wishlist = json_decode($wishlistJson, true);
 
        $productIds = array_column($wishlist, 'productId');
        $products = Product::whereIn('id', $productIds)->get();

        foreach ($products as $product) {
            foreach ($wishlist as $item) {
                if ($item['productId'] == $product->id) {
                    if ($item['offerId'] == 1) {
                        $product->offer_price = $item['price'];
                        $product->offer_id = 1; 
                    } elseif ($item['offerId'] == 2) {
                        $product->flash_sell_price = $item['price'];
                        $product->offer_id = 2;
                    } else {
                        $product->price = $product->stockhistory()
                          ->where('available_qty', '>', 0)
                          ->orderBy('id', 'asc')
                          ->value('selling_price') ?? $item['price'];
                        $product->offer_id = 0;
                    }
                    if (isset($item['campaignId'])) {
                        $product->campaign_id = $item['campaignId'];
                        $campaignRequestProduct = CampaignRequestProduct::find($item['campaignId']);
                        $product->quantity = $campaignRequestProduct->quantity;
                    }
                }
            }
        }

        return view('frontend.wish_list', compact('products'));
    }

    public function storeCart(Request $request)
    {
        $request->session()->put('cart', $request->input('cart'));
        return response()->json(['success' => true]);
    }

    public function showCart(Request $request)
    {
        $cartJson = $request->session()->get('cart', '[]');
        $cart = json_decode($cartJson, true);
        // dd($cart);
        return view('frontend.cart', compact('cart'));
    }

    public function checkout(Request $request)
    {
        $cart = json_decode($request->input('cart'), true);
        return view('frontend.checkout', compact('cart'));
    }

    public function search(Request $request)
    {
        $query = $request->input('query');
        $products = Product::where('name', 'LIKE', "%$query%")
                            ->where('status', 1)
                            ->whereDoesntHave('specialOfferDetails')
                            ->whereDoesntHave('flashSellDetails')
                            ->orderBy('id', 'desc')
                            ->take(15)
                            ->with('stock')
                            ->get();
    
        $products->each(function($product) {
            $product->price = $product->stockhistory()
                ->where('available_qty', '>', 0)
                ->orderBy('id', 'asc')
                ->value('selling_price') ?? $product->price;
    
            $product->colors = $product->stock()
                ->where('quantity', '>', 0)
                ->distinct('color')
                ->pluck('color');
    
            $product->sizes = $product->stock()
                ->where('quantity', '>', 0)
                ->distinct('size')
                ->pluck('size');
    
            return $product;
        });

        return response()->json(['products' => $products]);
    }
    
    public function shop(Request $request)
    {
        $currency = CompanyDetails::value('currency');

        $categories = Category::where('status', 1)
            ->whereHas('products.stock', function($query) {
                $query->where('quantity', '>', 0);
            })
            ->orderBy('id', 'desc')
            ->select('id', 'name')
            ->get();
            
        $brands = Brand::where('status', 1)
            ->whereHas('products.stock', function($query) {
                $query->where('quantity', '>', 0);
            })
            ->orderBy('id', 'desc')
            ->select('id', 'name')
            ->get();

        $colors = Stock::where('quantity', '>', 0)
            ->groupBy('color')
            ->select('color')
            ->get();

        $sizes = Stock::where('quantity', '>', 0)
            ->groupBy('size')
            ->select('size')
            ->get();

        $minPrice = StockHistory::where('status', 1)->min('selling_price'); 
        $maxPrice = StockHistory::where('status', 1)->max('selling_price');

        return view('frontend.shop', compact('currency', 'categories', 'brands', 'colors', 'sizes', 'minPrice', 'maxPrice'));
    }

    public function supplierPage($slug)
    {
        $currency = CompanyDetails::value('currency');
        $supplier = Supplier::where('slug', $slug)->firstOrFail();
        $company = CompanyDetails::select('company_name')->first();
        $title = $company->company_name . ' - ' . $supplier->name;

        $approvedStocks = SupplierStock::where('supplier_id', $supplier->id)
                                    ->where('is_approved', 1)
                                    ->select('product_id', 'price', 'quantity')
                                    ->get();

        $productIds = $approvedStocks->pluck('product_id');

        $products = Product::whereIn('id', $productIds)
                            ->with(['supplierStocks' => function ($query) {
                                $query->select('product_id', 'price', 'quantity');
                            }])
                            ->select('id', 'name', 'slug', 'price', 'feature_image')
                            ->get();

        return view('frontend.supplier_products', compact('supplier', 'title', 'currency', 'products'));
    }

    public function searchSupplierProducts(Request $request)
    {
        $query = $request->input('query');
        $supplierId = $request->input('supplier_id');

        $products = Product::whereHas('supplierStocks', function ($q) use ($supplierId) {
                                    $q->where('supplier_id', $supplierId);
                                })
                                ->where('name', 'LIKE', "%$query%")
                                ->where('status', 1)
                                ->orderBy('id', 'desc')
                                ->with(['supplierStocks' => function ($q) {
                                    $q->select('product_id', 'price', 'quantity');
                                }])
                                ->select('id', 'name', 'slug', 'price', 'feature_image')
                                ->take(15)
                                ->get();

        if ($products->isEmpty()) {
            return response()->json('<div class="p-2">No products found</div>');
        }

        $output = '<ul class="list-group">';
        foreach ($products as $product) {
            $output .= '<li class="list-group-item">
                            <a href="'.route('product.show.supplier', [$product->slug, $supplierId]).'">
                                '.$product->name.'
                            </a>
                        </li>';
        }
        $output .= '</ul>';

        return response()->json($output);
    }

    public function contact()
    {
        $companyDetails = CompanyDetails::select('google_map', 'address1', 'email1', 'phone1')->first();
        return view('frontend.contact', compact('companyDetails'));
    }

    public function storeContact(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required',
            'subject' => 'nullable|string|max:255',
            'message' => 'required|string',
        ]);

        $contact = new Contact();
        $contact->name = $request->input('name');
        $contact->email = $request->input('email');
        $contact->phone = $request->input('phone');
        $contact->subject = $request->input('subject');
        $contact->message = $request->input('message');
        $contact->save();

        return back()->with('success', 'Your message has been sent successfully!');
    }

    public function aboutUs()
    {
        $companyDetails = CompanyDetails::select('about_us')->first();
        return view('frontend.about', compact('companyDetails'));
    }

    public function checkCoupon(Request $request)
    {
        $coupon = Coupon::where('coupon_name', $request->coupon_name)->first();
    
        // Check if the coupon exists
        if (!$coupon) {
            return response()->json([
                'success' => false,
                'message' => 'Coupon not found.'
            ]);
        }

        // Check if the coupon is active
        if ($coupon->status != 1) {
            return response()->json([
                'success' => false,
                'message' => 'Coupon is inactive.'
            ]);
        }
    
        // Check coupon usage
        $totalUsage = CouponUsage::where('coupon_id', $coupon->id)->count();
        if ($coupon->total_max_use > 0 && $totalUsage >= $coupon->total_max_use) {
            return response()->json([
                'success' => false,
                'message' => 'Coupon has reached its maximum usage limit.'
            ]);
        }
    
        // Check max usage per user or guest
        if (auth()->check()) {
            $userId = auth()->user()->id;
            $userUsage = CouponUsage::where('coupon_id', $coupon->id)->where('user_id', $userId)->count();
    
            if ($coupon->max_use_per_user > 0 && $userUsage >= $coupon->max_use_per_user) {
                return response()->json([
                    'success' => false,
                    'message' => 'You have exceeded the limit for using this coupon.'
                ]);
            }
        } else {
            // Check max usage per guest based on either email or phone
            $guestEmail = $request->input('guest_email');
            $guestPhone = $request->input('guest_phone');
            
            $guestUsage = CouponUsage::where('coupon_id', $coupon->id)
                ->where(function ($query) use ($guestEmail, $guestPhone) {
                    if ($guestEmail) {
                        $query->where('guest_email', $guestEmail);
                    }
                    if ($guestPhone) {
                        $query->orWhere('guest_phone', $guestPhone);
                    }
                })
                ->count();
    
            if ($coupon->max_use_per_user > 0 && $guestUsage >= $coupon->max_use_per_user) {
                return response()->json([
                    'success' => false,
                    'message' => 'You have exceeded the limit for using this coupon.'
                ]);
            }
        }
    
        return response()->json([
            'success' => true,
            'coupon_id' => $coupon->id,
            'coupon_type' => $coupon->coupon_type,
            'coupon_value' => $coupon->coupon_value
        ]);
    }    
    

    public function filter(Request $request)
    {
        $startPrice = $request->input('start_price');
        $endPrice = $request->input('end_price');
        $categoryId = $request->input('category');
        $brandId = $request->input('brand');
        $size = $request->input('size');
        $color = $request->input('color');


        $productsQuery = Product::select('products.id', 'products.name', 'products.price', 'products.slug', 'products.feature_image')
                                ->where('products.status', 1)
                                ->leftJoin('stocks', 'products.id', '=', 'stocks.product_id')
                                ->whereDoesntHave('specialOfferDetails')
                                ->whereDoesntHave('flashSellDetails')
                                ->orderByRaw('COALESCE(stocks.quantity, 0) DESC')  //treating NULL stock values as 0
                                ->with('stock');
    
        if ($startPrice !== null && $endPrice !== null) {
            $productsQuery->whereBetween('products.price', [$startPrice, $endPrice]);
        }
    
        if (!empty($categoryId)) {
            $productsQuery->where('category_id', $categoryId);
        }

        if (!empty($brandId)) {
            $productsQuery->where('brand_id', $brandId);
        }

        if (!empty($size)) {
            $productsQuery->where('stocks.size', $size);
        }

        if (!empty($color)) {
            $productsQuery->where('stocks.color', $color);
        }

        $products = $productsQuery->get()->map(function ($product) {
            $product->price = $product->stockhistory()
                ->where('available_qty', '>', 0)
                ->orderBy('id', 'asc')
                ->value('selling_price') ?? $product->price; 

            $product->colors = $product->stock()
                ->where('quantity', '>', 0)
                ->distinct('color')
                ->pluck('color');

            $product->sizes = $product->stock()
                ->where('quantity', '>', 0)
                ->distinct('size')
                ->pluck('size');  
                 
            return $product;
        });

        return response()->json(['products' => $products]);
    }

    public function showCampaignDetails($slug)
    {
        $campaign = Campaign::where('slug', $slug)->firstOrFail();
        $campaignRequests = CampaignRequest::with(['supplier', 'campaignRequestProducts.product'])
            ->where('campaign_id', $campaign->id)
            ->where('status', 1)
            ->get();

        $company = CompanyDetails::select('company_name')
                    ->first();
        $title = $company->company_name . ' - ' . $campaign->title;
        $currency = CompanyDetails::value('currency');

        return view('frontend.campaign', compact('campaign', 'campaignRequests', 'title', 'currency'));
    }

    public function showCampaignProduct($slug, $supplierId = null)
    {
        $product = Product::where('slug', $slug)->firstOrFail();
     
        $campaignPrice = null;
        $campaignQuantity = null;

        if ($supplierId) {
            $campaignRequest = CampaignRequest::where('supplier_id', $supplierId)
                ->where('status', 1)
                ->first();

            if ($campaignRequest) {
                $campaignProduct = CampaignRequestProduct::where('campaign_request_id', $campaignRequest->id)
                    ->where('product_id', $product->id)
                    ->first();

                if ($campaignProduct) {
                    $campaignPrice = $campaignProduct->campaign_price;
                    $campaignQuantity = $campaignProduct->quantity;
                }
            }
        } else{
            $campaignProduct = CampaignRequestProduct::where('product_id', $product->id)
            ->whereHas('campaignRequest', function ($query) {
                $query->where('status', 1);
                $query->whereNull('supplier_id');
            })->first();

            if ($campaignProduct) {
                $campaignPrice = $campaignProduct->campaign_price;
                $campaignQuantity = $campaignProduct->quantity;
            }

        }

        // dd($campaignProduct);

        $product->watch = $product->watch + 1;
        $product->save();

        $company_name = CompanyDetails::value('company_name');
        $title = $company_name . ' - ' . $product->name;
        $currency = CompanyDetails::value('currency');

        return view('frontend.product.campaign_single_product', compact('product', 'campaignProduct', 'title', 'campaignPrice', 'currency', 'campaignQuantity'));
    }

    public function wholesaleProductDetails($slug)
    {
        $product = Product::where('slug', $slug)->firstOrFail();
        $title = $product->name;
        $currency = CompanyDetails::value('currency');
        $wholeSaleProduct = WholeSaleProduct::with('prices')
            ->where('product_id', $product->id)
            ->first();

        return view('frontend.product.wh_single_product', compact('product', 'title', 'currency', 'wholeSaleProduct'));
    }

    public function clearAllSessionData()
    {
        session()->flush();
        session()->regenerate();
        session(['session_clear' => true]);
        return redirect()->route('login');
    }

    public function storeReview(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'rating' => 'required|integer|between:1,5',
        ]);

        $review = new ProductReview();
        $review->user_id = auth()->id();
        $review->product_id = $request->product_id;
        $review->title = $request->title;
        $review->description = $request->description;
        $review->rating = $request->rating;
        $review->created_by = auth()->id();
        $review->save();
    
        return response()->json(['success' => true]);
    }

}
