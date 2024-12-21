<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SectionStatus;

class SectionController extends Controller
{
    public function sectionStatus()
    {
        $status = SectionStatus::firstOrCreate();
        return view('admin.section.section_status', compact('status'));
    }

    public function updateSectionStatus(Request $request)
    {
        $status = SectionStatus::firstOrCreate([]);

        $request->validate([
            'slider' => 'required|in:0,1',
            'special_offer' => 'required|in:0,1',
            'campaigns' => 'required|in:0,1',
            'features' => 'required|in:0,1',
            'categories' => 'required|in:0,1',
            'feature_products' => 'required|in:0,1',
            'flash_sell' => 'required|in:0,1',
            'recent_products' => 'required|in:0,1',
            'popular_products' => 'required|in:0,1',
            'trending_products' => 'required|in:0,1',
            'most_viewed_products' => 'required|in:0,1',
            'buy_one_get_one' => 'required|in:0,1',
            'category_products' => 'required|in:0,1',
            'bundle_products' => 'required|in:0,1',
            'vendors' => 'required|in:0,1',
        ]);

        $status->slider = $request->input('slider');
        $status->special_offer = $request->input('special_offer');
        $status->campaigns = $request->input('campaigns');
        $status->features = $request->input('features');
        $status->categories = $request->input('categories');
        $status->feature_products = $request->input('feature_products');
        $status->flash_sell = $request->input('flash_sell');
        $status->recent_products = $request->input('recent_products');
        $status->popular_products = $request->input('popular_products');
        $status->trending_products = $request->input('trending_products');
        $status->most_viewed_products = $request->input('most_viewed_products');
        $status->buy_one_get_one = $request->input('buy_one_get_one');
        $status->category_products = $request->input('category_products');
        $status->bundle_products = $request->input('bundle_products');
        $status->vendors = $request->input('vendors');

        $status->save();

        return redirect()->back()->with('success', 'Section statuses updated successfully');
    }

}
