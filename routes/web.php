<?php

use App\Http\Controllers\OrdersController;
use App\Http\Controllers\PackingController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/orders', [OrdersController::class, 'index'])->name('orders');

Route::get('/packing', [PackingController::class, 'index'])->name('packing');
