<?php

namespace App\Http\Controllers\Supplier;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\CampaignRequest;
use App\Models\SupplierStock;
use App\Models\Campaign;
use App\Models\CampaignRequestProduct;
use App\Models\Product;

class CampaignController extends Controller
{
    public function campaignRequest()
    {
        $supplierId = Auth::guard('supplier')->user()->id;
        
        $products = Product::orderBy('id', 'desc')->get();

        $campaigns = Campaign::orderBy('id', 'desc')->get();
        return view('supplier.campaign.request', compact('products', 'campaigns'));
    }

    public function campaignRequestStore(Request $request)
    {
        $request->validate([
            'campaign_id' => 'required|exists:campaigns,id',
            'products' => 'required|array',
            'products.*.product_id' => 'required|exists:products,id',
            'products.*.quantity' => 'required|numeric|min:1',
            'products.*.campaign_price' => 'required|numeric|min:0',
            'products.*.product_size' => 'nullable|string|max:255',
            'products.*.product_color' => 'nullable|string|max:255',
        ]);

        $campaignRequest = new CampaignRequest();
        $campaignRequest->supplier_id = Auth::guard('supplier')->user()->id;
        $campaignRequest->campaign_id = $request->campaign_id;
        $campaignRequest->created_by = Auth::guard('supplier')->user()->id; 
        $campaignRequest->save();

        foreach ($request->products as $product) {
            $campaignRequestProduct = new CampaignRequestProduct();
            $campaignRequestProduct->campaign_request_id = $campaignRequest->id;
            $campaignRequestProduct->product_id = $product['product_id'];
            $campaignRequestProduct->quantity = $product['quantity'];
            $campaignRequestProduct->campaign_price = $product['campaign_price'];
            $campaignRequestProduct->product_size = $product['product_size'];
            $campaignRequestProduct->product_color = $product['product_color'];
            $campaignRequestProduct->created_by = Auth::guard('supplier')->user()->id; 
            $campaignRequestProduct->save();
        }
        return response()->json(['message' => 'Campaign request created successfully.'], 200);
    }

    public function campaignRequests()
    {
        $supplierId = Auth::guard('supplier')->user()->id;
        $data = CampaignRequest::with('campaign')->where('supplier_id', $supplierId)->orderBy('id', 'desc')->get();
        return view('supplier.campaign.index', compact('data'));
    }

    public function getCampaignRequestDetails($id)
    {
        $campaignRequest = CampaignRequest::with('campaign', 'campaignRequestProducts.product')->find($id);

        if ($campaignRequest) {
            $products = $campaignRequest->campaignRequestProducts;
            return response()->json([
                'campaign' => $campaignRequest->campaign,
                'products' => $products
            ]);
        }

        return response()->json(['error' => 'Campaign request not found'], 404);
    }
}
