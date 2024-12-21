<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Campaign;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use App\Models\CampaignRequest;
use App\Models\Product;
use App\Models\CampaignRequestProduct;
use App\Models\Color;
use App\Models\Size;

class CampaignController extends Controller
{
    public function getCampaigns()
    {
        $data = Campaign::orderby('id','DESC')->get();
        return view('admin.campaign.index', compact('data'));
    }

    public function campaignStore(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'start_date' => 'required',
            'end_date' => 'required',
            'short_description' => 'nullable|string',
            'banner_image' => 'required|image|max:2048',
            'small_image' => 'required|image|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $campaign = new Campaign();
        $campaign->title = $request->title;
        $campaign->slug = Str::slug($request->title);
        $campaign->start_date = $request->start_date;
        $campaign->end_date = $request->end_date;
        $campaign->short_description = $request->short_description;
        $campaign->created_by = auth()->user()->id;

        if ($request->hasFile('banner_image')) {
            $image = $request->file('banner_image');
            $filename = mt_rand(10000000, 99999999) . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('images/campaign_banner'), $filename);
            $campaign->banner_image = $filename;
        }

        if ($request->hasFile('small_image')) {
            $image = $request->file('small_image');
            $filename = mt_rand(10000000, 99999999) . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('images/campaign_small'), $filename);
            $campaign->small_image = $filename;
        }

        $campaign->save();

        return response()->json(['message' => 'Campaign created successfully', 'data' => $campaign]);
    }

    public function campaignEdit($id)
    {
        $info = Campaign::where('id', $id)->first();
        return response()->json($info);
    }

    public function campaignUpdate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'start_date' => 'required',
            'end_date' => 'required',
            'short_description' => 'nullable|string',
            'banner_image' => 'nullable|image|max:2048',
            'small_image' => 'nullable|image|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $campaign = Campaign::find($request->codeid);
        $campaign->title = $request->title;
        $campaign->slug = Str::slug($request->title);
        $campaign->start_date = $request->start_date;
        $campaign->end_date = $request->end_date;
        $campaign->short_description = $request->short_description;
        $campaign->created_by = auth()->user()->id;

        if ($request->hasFile('banner_image')) {

            if ($campaign->banner_image && file_exists(public_path('images/campaign_banner/' . $campaign->banner_image))) {
                unlink(public_path('images/campaign_banner/' . $campaign->banner_image));
            }

            $image = $request->file('banner_image');
            $filename = mt_rand(10000000, 99999999) . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('images/campaign_banner'), $filename);
            $campaign->banner_image = $filename;
        }

        if ($request->hasFile('small_image')) {

            if ($campaign->small_image && file_exists(public_path('images/campaign_small/' . $campaign->small_image))) {
                unlink(public_path('images/campaign_small/' . $campaign->small_image));
            }

            $image = $request->file('small_image');
            $filename = mt_rand(10000000, 99999999) . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('images/campaign_small'), $filename);
            $campaign->small_image = $filename;
        }

        $campaign->save();

        return response()->json(['message' => 'Campaign created successfully', 'data' => $campaign]);
    }

    public function campaignDelete($id)
    {
        $campaign = Campaign::find($id);

        if ($campaign->banner_image && file_exists(public_path('images/campaign_banner/' . $campaign->banner_image))) {
            unlink(public_path('images/campaign_banner/' . $campaign->banner_image));
        }
        if ($campaign->small_image && file_exists(public_path('images/campaign_small/' . $campaign->small_image))) {
            unlink(public_path('images/campaign_small/' . $campaign->small_image));
        }
        $campaign->delete();
        return response()->json(['message' => 'Campaign deleted successfully']);
    }

    public function getCampaignRequests()
    {
        $products = Product::orderBy('id', 'desc')->get();
        $campaigns = Campaign::orderBy('id', 'desc')->get();

        $data = CampaignRequest::with(['campaign', 'supplier', 'campaignRequestProducts.product'])
            ->orderBy('created_at', 'desc')
            ->get();

        $colors = Color::select('id', 'color', 'color_code')->where('status', '1')->orderBy('id', 'desc')->get();
        $sizes = Size::select('id', 'size')->where('status', '1')->orderBy('id', 'desc')->get();
        return view('admin.campaign.requests', compact('data', 'products', 'campaigns', 'colors', 'sizes'));
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

    public function updateStatus(Request $request)
    {
        $request->validate([
            'campaign_request_id' => 'required|exists:campaign_requests,id',
            'status' => 'required|in:0,1,2',
        ]);

        $campaignRequest = CampaignRequest::find($request->campaign_request_id);
        $campaignRequest->status = $request->status;
        $campaignRequest->save();

        return response()->json([
            'success' => true,
            'status' => $campaignRequest->status,
        ]);
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
        $campaignRequest->campaign_id = $request->campaign_id;
        $campaignRequest->status = 1;
        $campaignRequest->created_by = auth()->user()->id; 
        $campaignRequest->save();

        foreach ($request->products as $product) {
            $campaignRequestProduct = new CampaignRequestProduct();
            $campaignRequestProduct->campaign_request_id = $campaignRequest->id;
            $campaignRequestProduct->product_id = $product['product_id'];
            $campaignRequestProduct->quantity = $product['quantity'];
            $campaignRequestProduct->campaign_price = $product['campaign_price'];
            $campaignRequestProduct->product_size = $product['product_size'];
            $campaignRequestProduct->product_color = $product['product_color'];
            $campaignRequestProduct->created_by = auth()->user()->id;
            $campaignRequestProduct->save();
        }
        return response()->json(['message' => 'Campaign request created successfully.'], 200);
    }

    public function showDetails($id)
    {
        $data = CampaignRequest::with(['campaign', 'supplier', 'campaignRequestProducts.product'])
                ->find($id);
        return view('admin.campaign.details', compact('data'));
    }

}
