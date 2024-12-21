@extends('admin.layouts.admin')

@section('content')
<div class="container pt-3 pb-5">
    <h2 class="my-4">Section Status</h2>
    
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <form action="{{ route('updateSectionStatus') }}" method="POST">
        @csrf

        <table class="table">
            <thead>
                <tr>
                    <th scope="col">Section</th>
                    <th scope="col">Status</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Slider</td>
                    <td>
                        <select name="slider" id="slider" class="form-control">
                            <option value="1" {{ $status->slider ? 'selected' : '' }}>On</option>
                            <option value="0" {{ !$status->slider ? 'selected' : '' }}>Off</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td>Special Offer</td>
                    <td>
                        <select name="special_offer" id="special_offer" class="form-control">
                            <option value="1" {{ $status->special_offer ? 'selected' : '' }}>On</option>
                            <option value="0" {{ !$status->special_offer ? 'selected' : '' }}>Off</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td>Campaigns</td>
                    <td>
                        <select name="campaigns" id="campaigns" class="form-control">
                            <option value="1" {{ $status->campaigns ? 'selected' : '' }}>On</option>
                            <option value="0" {{ !$status->campaigns ? 'selected' : '' }}>Off</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td>Features</td>
                    <td>
                        <select name="features" id="features" class="form-control">
                            <option value="1" {{ $status->features ? 'selected' : '' }}>On</option>
                            <option value="0" {{ !$status->features ? 'selected' : '' }}>Off</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td>Categories</td>
                    <td>
                        <select name="categories" id="categories" class="form-control">
                            <option value="1" {{ $status->categories ? 'selected' : '' }}>On</option>
                            <option value="0" {{ !$status->categories ? 'selected' : '' }}>Off</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td>Feature Products</td>
                    <td>
                        <select name="feature_products" id="feature_products" class="form-control">
                            <option value="1" {{ $status->feature_products ? 'selected' : '' }}>On</option>
                            <option value="0" {{ !$status->feature_products ? 'selected' : '' }}>Off</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td>Flash Sell</td>
                    <td>
                        <select name="flash_sell" id="flash_sell" class="form-control">
                            <option value="1" {{ $status->flash_sell ? 'selected' : '' }}>On</option>
                            <option value="0" {{ !$status->flash_sell ? 'selected' : '' }}>Off</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td>Recent Products</td>
                    <td>
                        <select name="recent_products" id="recent_products" class="form-control">
                            <option value="1" {{ $status->recent_products ? 'selected' : '' }}>On</option>
                            <option value="0" {{ !$status->recent_products ? 'selected' : '' }}>Off</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td>Popular Products</td>
                    <td>
                        <select name="popular_products" id="popular_products" class="form-control">
                            <option value="1" {{ $status->popular_products ? 'selected' : '' }}>On</option>
                            <option value="0" {{ !$status->popular_products ? 'selected' : '' }}>Off</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td>Trending Products</td>
                    <td>
                        <select name="trending_products" id="trending_products" class="form-control">
                            <option value="1" {{ $status->trending_products ? 'selected' : '' }}>On</option>
                            <option value="0" {{ !$status->trending_products ? 'selected' : '' }}>Off</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td>Most Viewed Products</td>
                    <td>
                        <select name="most_viewed_products" id="most_viewed_products" class="form-control">
                            <option value="1" {{ $status->most_viewed_products ? 'selected' : '' }}>On</option>
                            <option value="0" {{ !$status->most_viewed_products ? 'selected' : '' }}>Off</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td>Buy One Get One</td>
                    <td>
                        <select name="buy_one_get_one" id="buy_one_get_one" class="form-control">
                            <option value="1" {{ $status->buy_one_get_one ? 'selected' : '' }}>On</option>
                            <option value="0" {{ !$status->buy_one_get_one ? 'selected' : '' }}>Off</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td>Category Products</td>
                    <td>
                        <select name="category_products" id="category_products" class="form-control">
                            <option value="1" {{ $status->category_products ? 'selected' : '' }}>On</option>
                            <option value="0" {{ !$status->category_products ? 'selected' : '' }}>Off</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td>Bundle Products</td>
                    <td>
                        <select name="bundle_products" id="bundle_products" class="form-control">
                            <option value="1" {{ $status->bundle_products ? 'selected' : '' }}>On</option>
                            <option value="0" {{ !$status->bundle_products ? 'selected' : '' }}>Off</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td>Vendors</td>
                    <td>
                        <select name="vendors" id="vendors" class="form-control">
                            <option value="1" {{ $status->vendors ? 'selected' : '' }}>On</option>
                            <option value="0" {{ !$status->vendors ? 'selected' : '' }}>Off</option>
                        </select>
                    </td>
                </tr>
            </tbody>
        </table>

        <button type="submit" class="btn btn-secondary">Update Status</button>
    </form>
</div>
@endsection