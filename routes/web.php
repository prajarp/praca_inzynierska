<?php

use App\Http\Controllers\OrdersController;
use App\Http\Controllers\OrdersSelectionController;
use App\Http\Controllers\PackingController;
use App\Http\Controllers\SelectedOrderController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/orders', [OrdersController::class, 'index'])->name('orders');

Route::get('/packing', [PackingController::class, 'index'])->name('packing');

Route::prefix('orders')->name('orders.')->group(function () {
    Route::resource('list', OrdersSelectionController::class);
    Route::resource('selected', SelectedOrderController::class);
});
