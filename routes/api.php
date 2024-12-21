<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\PassportAuthController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\HomeController;

Route::get('home', [HomeController::class, 'index']);
Route::get('shop', [HomeController::class, 'shop']);
Route::get('about-us', [HomeController::class, 'aboutUs']);
Route::get('contact', [HomeController::class, 'contact']);
Route::post('contact', [HomeController::class, 'contactStore']);

Route::get('product/{slug}', [HomeController::class, 'showProduct']);
Route::get('product/{slug}/{offerId?}', [HomeController::class, 'showProduct']);
Route::get('sup-product/{slug}/{supplierId?}', [HomeController::class, 'showSupplierProduct']);
Route::get('category/{slug}', [HomeController::class, 'showCategoryProducts']);
Route::get('sub-category/{slug}', [HomeController::class, 'showSubCategoryProducts']);

Route::get('bogo/{slug}', [HomeController::class, 'bogoShowProduct']);
Route::get('bundle/{slug}', [HomeController::class, 'bundleSingleProduct']);

Route::get('special-offers/{slug}', [HomeController::class, 'specialOffer']);
Route::get('flash-sells/{slug}', [HomeController::class, 'flashSell']);

Route::get('ads', [HomeController::class, 'getAllAds']);

Route::get('sliders', [HomeController::class, 'getAllSliders']);

Route::get('featured-products', [HomeController::class, 'getFeaturedProducts']);
Route::get('trending-products', [HomeController::class, 'getTrendingProducts']);
Route::get('recent-products', [HomeController::class, 'getRecentProducts']);
Route::get('popular-products', [HomeController::class, 'getPopularProducts']);
Route::get('category-products', [HomeController::class, 'getAllCategoryProducts']);


Route::post('register', [PassportAuthController::class, 'register']);
Route::post('login', [PassportAuthController::class, 'login']);

Route::group(['middleware' => ['auth:api']], function () {
    Route::get('user-details', [UserController::class, 'userDetails']);
    Route::put('password-change',[PassportAuthController::class, 'changePassword']);
    Route::post('user-profile-update', [UserController::class, 'userProfileUpdate']);

    Route::get('orders', [UserController::class, 'getOrders']);
    Route::get('orders/details/{orderId}', [UserController::class, 'orderDetails']);

});