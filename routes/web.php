<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\OrderSuccessController;
use App\Http\Controllers\OrderTrackingController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\StorefrontController;
use Illuminate\Support\Facades\Route;

Route::get('/', [StorefrontController::class, 'home'])->name('home');
Route::get('/products', [StorefrontController::class, 'catalog'])->name('products.index');
Route::get('/products/{slug}', [ProductController::class, 'show'])
    ->where('slug', '[A-Za-z0-9-]+')
    ->name('products.show');
Route::post('/products/{slug}/consultation', [ContactController::class, 'storeProductConsultation'])
    ->where('slug', '[A-Za-z0-9-]+')
    ->name('products.consultation');
Route::get('/product/detail.php', [ProductController::class, 'showLegacyId'])->name('products.legacy');

Route::get('/contact', [ContactController::class, 'index'])->name('contact');
Route::post('/contact', [ContactController::class, 'store']);
Route::get('/news', [NewsController::class, 'index'])->name('news.index');
Route::get('/login', [AuthController::class, 'loginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'registerForm'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::post('/cart', [CartController::class, 'update'])->name('cart.update');
Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
Route::get('/cart/checkout', [CheckoutController::class, 'index'])
    ->name('cart.checkout')
    ->middleware('throttle:30,1');
Route::post('/cart/checkout', [CheckoutController::class, 'store'])
    ->name('cart.checkout.store')
    ->middleware('throttle:10,1');
Route::get('/orders/track', [OrderTrackingController::class, 'index'])->name('orders.track');
Route::post('/orders/track', [OrderTrackingController::class, 'lookup'])
    ->middleware('throttle:10,1')
    ->name('orders.track.lookup');
Route::get('/orders/success', OrderSuccessController::class)->name('orders.success');
