<?php
  
use Illuminate\Support\Facades\Route;
  
use App\Http\Controllers\HomeController;
use App\Http\Controllers\FrontendController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\Admin\SpecialOfferController;
use App\Http\Controllers\Admin\FlashSellController;
use App\Http\Controllers\Supplier\SupplierAuthController;
use App\Http\Controllers\Supplier\SupplierController;
use App\Http\Controllers\Supplier\StockController;
use App\Http\Controllers\Supplier\CampaignController;
use App\Http\Controllers\Auth\LoginController;
  

// cache clear
Route::get('/clear', function() {
    Auth::logout();
    session()->flush();
    Artisan::call('cache:clear');
    Artisan::call('config:clear');
    Artisan::call('config:cache');
    Artisan::call('view:clear');
    return "Cleared!";
 });
//  cache clear
  
  
Auth::routes();

Route::get('/clear-session', [FrontendController::class, 'clearAllSessionData'])->name('clearSessionData');

Route::fallback(function () {
    return redirect('/');
});
  
Route::get('/', [FrontendController::class, 'login']);
Route::get('/order/{encoded_order_id}', [OrderController::class, 'generatePDF'])->name('generate-pdf');