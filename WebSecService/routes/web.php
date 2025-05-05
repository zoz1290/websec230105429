<?php
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\ProductsController;
use App\Http\Controllers\Web\UsersController;
use App\Http\Controllers\Web\PurchaseController;

// User

Route::get('register', [UsersController::class, 'register'])->name('register');
Route::post('register', [UsersController::class, 'doRegister'])->name('do_register');
Route::get('login', [UsersController::class, 'login'])->name('login');
Route::post('login', [UsersController::class, 'doLogin'])->name('do_login');
Route::get('logout', [UsersController::class, 'doLogout'])->name('do_logout');
Route::get('users', [UsersController::class, 'list'])->name('users');
Route::get('profile/{user?}', [UsersController::class, 'profile'])->name('profile');
Route::get('users/edit/{user?}', [UsersController::class, 'edit'])->name('users_edit');
Route::put('users/save/{user}', [UsersController::class, 'save'])->name('users_save');
Route::delete('users/delete/{user}', [UsersController::class, 'delete'])->name('users_delete');
Route::get('users/edit_password/{user?}', [UsersController::class, 'editPassword'])->name('edit_password');
Route::post('users/save_password/{user}', [UsersController::class, 'savePassword'])->name('save_password');


// Page load
Route::get('users/create', [UsersController::class, 'create'])->name('users_create');

// Do Logic : Form Submit
Route::post('users', [UsersController::class, 'store'])->name('users_store');

Route::get('customers', [UsersController::class, 'customers'])->name('customers');
Route::get('customers/edit_credit/{user?}', [UsersController::class, 'editCredit'])->name('customers_edit_credit'); // Display view
Route::put('customers/update_credit/{user}', [UsersController::class, 'updateCredit'])->name('customers_update_credit'); // Do Logic
Route::get('customers/reset_credit/{user?}', action: [UsersController::class, 'resetCredit'])->name('reset_credit'); // Display view



// Products
Route::get('products', [ProductsController::class, 'list'])->name('products_list');
Route::get('products/edit/{product?}', [ProductsController::class, 'edit'])->name('products_edit');
Route::post('products/save/{product?}', [ProductsController::class, 'save'])->name('products_save');
Route::delete('products/delete/{product}', [ProductsController::class, 'delete'])->name('products_delete');
Route::post('product/discount/{product?}', [ProductsController::class, 'discount'])->name('products_discount');

// Purchase
Route::post('purchase', [PurchaseController::class, 'purchase'])->name('purchase'); // Do Logic
Route::get('purchased', [purchaseController::class, 'purchasedProducts'])->name('purchased_products');// Display View


Route::get('/', function () {
    return view('welcome');
});

Route::get('/multable', function (Request $request) {
    $j = $request->number??5;
    $msg = $request->msg;
    return view('multable', compact("j", "msg"));
});

Route::get('/even', function () {
    return view('even');
});

Route::get('/prime', function () {
    return view('prime');
});

Route::get('/test', function () {
    return view('test');
});

Route::get('/alphabits', [UsersController::class, 'alphabits'])->name('alphabits');

Route::post('products/purchase/{product}', [ProductsController::class, 'purchase'])->name('products_purchase');
Route::get('products/purchased', [ProductsController::class, 'listPurchasedProducts'])->name('products_purchased');

// Google Auth Routes
Route::get('/auth/google', [UsersController::class, 'redirectToGoogle'])->name('google.login');
Route::get('/auth/google/callback', [UsersController::class, 'handleGoogleCallback'])->name('google.callback');

// Facebook Auth Routes
Route::get('/auth/facebook', [UsersController::class, 'redirectToFacebook'])->name('facebook.login');
Route::get('/auth/facebook/callback', [UsersController::class, 'handleFacebookCallback'])->name('facebook.callback');

// Naming convention for routes 
// Route::get('users', [UsersController::class, 'index'])->name('users.index'); 
// Route::get('users/create', [UsersController::class, 'create'])->name('users.create');
// Route::post('users', [UsersController::class, 'store'])->name('users.store');
// Route::get('users/{user}', [UsersController::class, 'show'])->name('users.show');
// Route::get('users/{user}/edit', [UsersController::class, 'edit'])->name('users.edit');
// Route::put('users/{user}', [UsersController::class, 'update'])->name('users.update');
// Route::delete('users/{user}', [UsersController::class, 'destroy'])->name('users.destroy');
// Route::get('verify', [UsersController::class, 'verify'])->name('verify');
