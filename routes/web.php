<?php

use App\Http\Controllers\Controller;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;

// Route::get('/', [Controller::class, 'index']);
<<<<<<< HEAD
Route::get('/', [ProductController::class, 'view'])->name('home');
=======
Route::redirect('/', '/index')->name('home');
Route::get('/store', [ProductController::class, 'cashier'])
    ->middleware('auth')
    ->name('store');
>>>>>>> 25c60694e0b26e50d6294b21eb7fad26f4e1aa21
Route::get('/index', function () {
    return view('index');
})->name('index');
Route::view('/about', 'about')->name('about');
Route::get('/db', function () {
    return redirect('https://docs.google.com/spreadsheets/d/1q6eQ7FPGr89_bXqp8Kf_ra1E_1omKGfd/edit?gid=512633841#gid=512633841');
<<<<<<< HEAD
})->middleware('auth');
Route::get('/appr', [ProductController::class, 'updateappr'])->middleware('auth');
Route::get('/run', [ProductController::class, 'runcommand'])->middleware('auth');
Route::get('/login', [CustomerController::class, 'index'])->name('login');
Route::get('/logout', [CustomerController::class, 'logout'])->name('customer.logout');
Route::get('/dashboard', [CustomerController::class, 'dashboard'])->name('dashboard');
Route::get('/checkout/{id}', [ProductController::class, 'index'])->name('checkout')->middleware('auth');
Route::get('/test/{id}', [ProductController::class, 'test'])->name('test')->middleware('auth');
=======
});
Route::get('/appr', [ProductController::class, 'updateappr']);
Route::get('/run', [ProductController::class, 'runcommand']);
Route::get('/login', [CustomerController::class, 'index'])->name('login');
Route::get('/logout', [CustomerController::class, 'logout'])->name('customer.logout');
Route::get('/dashboard', [CustomerController::class, 'dashboard'])->name('dashboard');
Route::get('/checkout/{id}', [ProductController::class, 'index'])->name('checkout');
Route::get('/test/{id}', [ProductController::class, 'test'])->name('test');
>>>>>>> 25c60694e0b26e50d6294b21eb7fad26f4e1aa21
Route::get('/customer', [CustomerController::class, 'index'])->name('customer');
Route::get('/order/export/{id}', [CustomerController::class, 'exportSingle'])
    ->name('orders.export.single')
    ->middleware('auth');
Route::get('/dashboard/export-orders', [CustomerController::class, 'export'])
    ->name('orders.export')
    ->middleware('auth');

require __DIR__.'/settings.php';
