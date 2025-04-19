<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\TransactionController;

Route::middleware(['IsGuest'])->group(function(){
    Route::get('/',function(){
        return view('login');
    })->name('login');
        Route::post('/login', [UserController::class, 'login'])->name('login.auth');
});

Route::middleware('IsLogin')->group(function() {
    Route::get('/dashboard', [UserController::class, 'dashboard'])->name('dashboard');
    Route::get('/logout', [UserController::class, 'logout'])->name('logout');

    Route::middleware('IsAdmin')->group(function() {
        Route::prefix('products')->name('products.')->group(function(){
            Route::get('/',[ProductController::class, 'index'])->name('index');
            Route::get('/create',[ProductController::class, 'create'])->name('create');
            Route::post('/store',[ProductController::class, 'store'])->name('store');
            Route::get('/up/{id}',[ProductController::class, 'up'])->name('up');
            Route::patch('/update/{id}',[ProductController::class,'update'])->name('update');
            Route::get('/up-stock/{id}',[ProductController::class, 'upStock'])->name('up-stock');
            Route::patch('/update-stock/{id}', [ProductController::class, 'updateStock'])->name('update-stock');
            Route::delete('/delete/{id}',[ProductController::class, 'destroy'])->name('destroy');
        });

        Route::prefix('transactions')->name('transactions.')->group(function(){
            Route::get('/',[TransactionController::class, 'index'])->name('index');
            Route::get('/receipt/{id}',[TransactionController::class, 'downloadReceipt'])->name('receipt');
        });

        Route::prefix('users')->name('users.')->group(function(){
            Route::get('/',[UserController::class, 'index'])->name('index');
            Route::get('/create',[UserController::class, 'create'])->name('create');
            Route::post('/store',[UserController::class, 'store'])->name('store');
            Route::get('/up/{id}',[UserController::class, 'up'])->name('up');
            Route::patch('/update/{id}',[UserController::class,'update'])->name('update');
            Route::delete('/delete/{id}',[UserController::class, 'destroy'])->name('destroy');
        });
    });

    Route::middleware('IsStaff')->group(function() {
        Route::prefix('products')->name('products2.')->group(function(){
            Route::get('/index',[ProductController::class, 'index2'])->name('index2');
        });

        Route::prefix('transactions')->name('transactions.')->group(function(){
            Route::get('/index',[TransactionController::class, 'index2'])->name('index2');
            Route::get('/create',[TransactionController::class, 'create'])->name('create');
            Route::post('/finalize', [TransactionController::class, 'finalize'])->name('finalize');
            Route::post('/receiption/{id}', [TransactionController::class, 'receiption'])->name('receiption');
            Route::post('/confirm', [TransactionController::class, 'confirm'])->name('confirm');
            Route::get('/show/{id}',[TransactionController::class, 'show'])->name('show');
            Route::get('/export', [TransactionController::class, 'export'])->name('export');
            Route::get('/receipt2/{id}',[TransactionController::class, 'downloadReceipt'])->name('receipt2');
        });
    });
});