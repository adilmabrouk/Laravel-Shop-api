<?php

use App\Http\Controllers\Buyer\BuyerController;
use App\Http\Controllers\Buyer\BuyerProductController;
use App\Http\Controllers\Buyer\BuyerSellerController;
use App\Http\Controllers\Buyer\BuyerTransactionController;
use App\Http\Controllers\Category\CategoryBuyerController;
use App\Http\Controllers\Category\CategoryController;
use App\Http\Controllers\Category\CategoryProductController;
use App\Http\Controllers\Category\CategorySellerController;
use App\Http\Controllers\Category\CategoryTransactionController;
use App\Http\Controllers\Product\ProductBuyerController;
use App\Http\Controllers\Product\ProductCategoryController;
use App\Http\Controllers\Product\ProductController;
use App\Http\Controllers\Product\ProductTransactionController;
use App\Http\Controllers\Seller\SellerController;
use App\Http\Controllers\Seller\SellerProductController;
use App\Http\Controllers\Seller\SellerTransactionController;
use App\Http\Controllers\Transaction\TransactionCategoryController;
use App\Http\Controllers\Transaction\TransactionController;
use App\Http\Controllers\Transaction\TransactionSellerController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\User\UserController;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware('auth:sanctum')->get('/buyers', function () {

// });

// Route::group(['middleware' => 'auth:sanctum'], function () {
//     Route::resource('buyers', BuyerController::class)->only('index', 'show');
// });

/**
 * Users
 */

Route::resource('user', UserController::class)->except('create', 'edit');
//Route::name('verify')->get('users/verify/{token}', 'User\UserController@verify');
Route::get('user/verify/{token}', [UserController::class, 'verify'])->name('verify');

/**
 * Buyers
 */

Route::resource('buyers', BuyerController::class)->only('index', 'show');
Route::resource('buyers.transactions', BuyerTransactionController::class)->only('index');
Route::resource('buyers.products', BuyerProductController::class)->only('index');
Route::resource('buyers.sellers', BuyerSellerController::class)->only('index');

/**
 * Sellers
 */

Route::resource('sellers', SellerController::class)->only('index', 'show');
Route::resource('sellers.transactions', SellerTransactionController::class)->only('index');
Route::resource('sellers.products', SellerProductController::class)->only('index', 'store', 'update', 'destroy');

/**
 * Categories
 */

Route::resource('categories', CategoryController::class)->except('create', 'edit');
Route::resource('categories.products', CategoryProductController::class)->only('index');
Route::resource('categories.sellers', CategorySellerController::class)->only('index');
Route::resource('categories.transactions', CategoryTransactionController::class)->only('index');
Route::resource('categories.buyers', CategoryBuyerController::class)->only('index');

/**
 * Products
 */

Route::resource('products', ProductController::class)->only('index', 'show');
Route::resource('products.transactions', ProductTransactionController::class)->only('index');
Route::resource('products.buyers', ProductBuyerController::class)->only('index');
Route::resource('products.categories', ProductCategoryController::class)->only('index');

/**
 * Transactions
 */

Route::resource('transactions', TransactionController::class)->only('index', 'show');
Route::resource('transaction.categories', TransactionCategoryController::class)->only('index');
Route::resource('transaction.sellers', TransactionSellerController::class)->only('index');
