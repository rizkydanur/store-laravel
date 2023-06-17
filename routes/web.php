<?php

use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DetailController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DashboardProductController;
use App\Http\Controllers\DashboardTransactionController;
use App\Http\Controllers\DashboardSettingController;
use App\Http\Controllers\Admin\DashboardAdminController;
use App\Http\Controllers\Admin\CategoriController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\ProductGalleryController;
use App\Http\Controllers\Admin\TransactionController;
use App\Http\Controllers\CheckoutController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/


// Named Route
Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/categories', [CategoryController::class, 'index'])->name('categories');
Route::get('/categories/{id}', [CategoryController::class, 'detail'])->name('categories-detail');

Route::get('/detail/{id?}', [DetailController::class, 'index'])->name('detail');
Route::post('/detail/{id?}', [DetailController::class, 'add'])->name('detail-add');




Route::post('/checkout/callback', [CheckoutController::class, 'callback'])->name('midtrans-callback');

Route::get('/success', [CartController::class, 'success'])->name('success');

Route::get('register/success', [RegisterController::class, 'success'])->name('register.success');



Route::group(['middleware' => ['auth']], function() {
    Route::get('/cart', [CartController::class, 'index'])->name('cart');
    Route::get('/cart/{id}', [CartController::class, 'destroy'])->name('cart-delete');

    Route::post('/checkout', [CheckoutController::class, 'process'])->name('checkout');

    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('dashboard/products', [DashboardProductController::class, 'index'])
    ->name('dashboard-products');
    Route::get('dashboard/products/create', [DashboardProductController::class, 'create'])
    ->name('dashboard-products-create');
    Route::post('dashboard/products/create', [DashboardProductController::class, 'store'])
    ->name('dashboard-products-store');
    Route::get('dashboard/products/details/{id}', [DashboardProductController::class, 'details'])
    ->name('dashboard-products-details');
    Route::post('dashboard/products/update/{id}', [DashboardProductController::class, 'update'])
    ->name('dashboard-products-update');

     Route::post('dashboard/products/gallery/upload', [DashboardProductController::class, 'uploadGallery'])
    ->name('dashboard-products-gallery-upload');
     Route::get('dashboard/products/gallery{id}', [DashboardProductController::class, 'deleteGallery'])
    ->name('dashboard-products-gallery-delet');

    Route::get('dashboard/transactions', [DashboardTransactionController::class, 'index'])
    ->name('dashboard-transactions');
    Route::get('dashboard/transactions/{id}', [DashboardTransactionController::class, 'details'])
    ->name('dashboard-transactions-details');
    Route::post('dashboard/transactions/update{id}', [DashboardTransactionController::class, 'update'])
    ->name('dashboard-transactions-update');

    Route::get('dashboard/settings', [DashboardSettingController::class, 'store'])
    ->name('dashboard-settings-store');
    Route::get('dashboard/account', [DashboardSettingController::class, 'account'])
    ->name('dashboard-settings-account');
    Route::post('dashboard/account/{redirect}', [DashboardSettingController::class, 'update'])
    ->name('dashboard-settings-redirect');



});

//->middleware(['auth','admin'])

Route::prefix('admin')
->namespace('Admin')
->middleware(['auth','admin'])
->group(function() {
    Route::get('/', [DashboardAdminController::class, 'index'])->name('admin-dashboard');
    Route::resource('categori', CategoriController::class);
    Route::resource('user', UserController::class);
    Route::resource('product', ProductController::class);
    Route::resource('product-gallery', ProductGalleryController::class);
    Route::resource('transaction', TransactionController::class);
});


// Resource Controller
Route::resource('home', HomeController::class);
Route::resource('categories', CategoryController::class);
Route::resource('cart', CartController::class);
Route::resource('cart-delete', CartController::class);
Route::resource('detail', DetailController::class);
Route::resource('register.success', RegisterController::class);
Route::resource('dashboard', DashboardController::class);
Route::resource('dashboard-products', DashboardproductController::class);
Route::resource('dashboard-products-details', DashboardproductController::class);
Route::resource('dashboard-products-create', DashboardproductController::class);
Route::resource('dashboard-products-store', DashboardproductController::class);
Route::resource('dashboard-gallery-update', DashboardproductController::class);
Route::resource('dashboard-products-gallery-delet', DashboardproductController::class);
Route::resource('dashboard-transactions', DashboardTransactionController::class);
Route::resource('dashboard-transactions/details', DashboardTransactionController::class);
Route::resource('dashboard-settings-store', DashboardSettingController::class);
Route::resource('dashboard-settings-account', DashboardSettingController::class);
Route::resource('dashboard-settings-redirect', DashboardSettingController::class);
Route::resource('dashboard-transactions-update', DashboardTransactionController::class);
Route::resource('admin-dashboard', DashboardAdminController::class);
Route::resource('categori', CategoriController::class);
Route::resource('user', UserController::class);
Route::resource('product', ProductController::class);
Route::resource('product-gallery', ProductGalleryController::class);
Route::resource('transaction', TransactionController::class);





Auth::routes();




