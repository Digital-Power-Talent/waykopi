<?php

use App\Http\Controllers\Admin\ShippingLabelController;
use App\Http\Controllers\Webhooks\XenditWebhookController;
use App\Livewire\Account\Dashboard as AccountDashboard;
use App\Livewire\Admin\CustomerManager as AdminCustomerManager;
use App\Livewire\Admin\Dashboard as AdminDashboard;
use App\Livewire\Admin\OrderManager as AdminOrderManager;
use App\Livewire\Admin\PostManager as AdminPostManager;
use App\Livewire\Admin\ProductManager as AdminProductManager;
use App\Livewire\Storefront\AboutUs;
use App\Livewire\Storefront\BlogDetail;
use App\Livewire\Storefront\BlogIndex;
use App\Livewire\Storefront\CartIndex;
use App\Livewire\Storefront\CheckoutPage;
use App\Livewire\Storefront\OrderSuccessPage;
use App\Livewire\Storefront\PaymentPage;
use App\Livewire\Storefront\ProductCatalog;
use App\Livewire\Storefront\ProductDetail;
use Illuminate\Support\Facades\Route;

// Storefront Routes
Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::get('/about', AboutUs::class)->name('about.index');
Route::get('/products', ProductCatalog::class)->name('products.index');
Route::get('/products/{slug}', ProductDetail::class)->name('products.show');

Route::get('/blog', BlogIndex::class)->name('blog.index');
Route::get('/blog/{slug}', BlogDetail::class)->name('blog.show');

Route::get('/cart', CartIndex::class)->name('cart.index');
Route::get('/checkout', CheckoutPage::class)->middleware('throttle:60,1')->name('checkout.index');

Route::get('/checkout/{orderNumber}/payment', PaymentPage::class)->name('checkout.payment');
Route::get('/checkout/{orderNumber}/success', OrderSuccessPage::class)->name('checkout.success');

// Webhook Routes (CSRF Excluded in bootstrap/app.php)
Route::post('/webhooks/xendit', XenditWebhookController::class)->middleware('throttle:60,1')->name('webhooks.xendit');
Route::post('/webhooks/biteship', \App\Http\Controllers\Webhooks\BiteshipWebhookController::class)->middleware('throttle:60,1')->name('webhooks.biteship');

// Customer Account Dashboard (Gated by Auth Middleware)
Route::get('/account', AccountDashboard::class)->middleware(['auth'])->name('account');

// Admin Route Group (Gated by auth & EnsureIsAdmin middleware)
Route::prefix('admin')->middleware(['auth', 'admin'])->group(function () {
    Route::get('/', AdminDashboard::class)->name('admin.dashboard');
    Route::get('/orders', AdminOrderManager::class)->name('admin.orders.index');
    Route::get('/orders/{order}/shipping-label', [ShippingLabelController::class, 'show'])->name('admin.orders.shipping-label');
    Route::get('/products', AdminProductManager::class)->name('admin.products.index');
    Route::get('/posts', AdminPostManager::class)->name('admin.posts.index');
    Route::get('/customers', AdminCustomerManager::class)->name('admin.customers.index');
});

require __DIR__ . '/auth.php';
