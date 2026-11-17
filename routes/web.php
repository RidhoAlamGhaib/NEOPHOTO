<?php

use App\Http\Controllers\BoothController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\CustomerMembershipController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\ProductController;
use App\Http\Middleware\EnsureDashboardAccess;
use Illuminate\Support\Facades\Route;

// Route::get('/', [Controller::class, 'index']);
Route::get('/store', [ProductController::class, 'view'])->name('store')->middleware('auth');
Route::get('/', function () {
    return view('index');
})->name('index');
Route::post('/membership/register', [CustomerMembershipController::class, 'register'])
    ->middleware('throttle:6,1')
    ->name('membership.register');
Route::post('/membership/login', [CustomerMembershipController::class, 'login'])
    ->middleware('throttle:6,1')
    ->name('membership.login');
Route::post('/membership/logout', [CustomerMembershipController::class, 'logout'])
    ->middleware('auth:member')
    ->name('membership.logout');
Route::get('/members', [MemberController::class, 'index'])
    ->middleware('auth:member')
    ->name('members.index');
Route::get('/members/profile/edit', [MemberController::class, 'editProfile'])
    ->middleware('auth:member')
    ->name('members.profile.edit');
Route::patch('/members/profile/edit', [MemberController::class, 'updateProfile'])
    ->middleware('auth:member')
    ->name('members.profile.details.update');
Route::post('/members/profile/edit', [MemberController::class, 'updateProfilePicture'])
    ->middleware(['auth:member', 'throttle:6,1'])
    ->name('members.profile.update');
Route::view('/about', 'about')->name('about');
Route::get('/db', function () {
    return redirect('https://docs.google.com/spreadsheets/d/1q6eQ7FPGr89_bXqp8Kf_ra1E_1omKGfd/edit?gid=512633841#gid=512633841');
})->middleware('auth');
Route::get('/appr', [ProductController::class, 'updateappr'])->middleware('auth');
Route::get('/run', [ProductController::class, 'runcommand'])->middleware('auth');
Route::get('/login', [CustomerController::class, 'index'])->name('login');
Route::get('/logout', [CustomerController::class, 'logout'])->name('customer.logout');
Route::get('/dashboard', [CustomerController::class, 'dashboard'])
    ->middleware(EnsureDashboardAccess::class)
    ->name('dashboard');
Route::get('/checkout/{id}', [ProductController::class, 'index'])->name('checkout')->middleware('auth');
Route::get('/test/{id}', [ProductController::class, 'test'])->name('test')->middleware('auth');
Route::get('/customer', [CustomerController::class, 'index'])->name('customer');
Route::get('/order/export/{id}', [CustomerController::class, 'exportSingle'])
    ->name('orders.export.single')
    ->middleware('auth');
Route::get('/dashboard/export-orders', [CustomerController::class, 'export'])
    ->name('orders.export')
    ->middleware(['auth', EnsureDashboardAccess::class]);

Route::get('/booth/bridge', [BoothController::class, 'bridge']);
Route::post('/booth/session', [BoothController::class, 'session'])->middleware('throttle:120,1');
Route::post('/booth/promo', [BoothController::class, 'promo'])->middleware('throttle:60,1');
Route::post('/booth/coupon', [BoothController::class, 'coupon'])->middleware('throttle:60,1');

require __DIR__.'/settings.php';
