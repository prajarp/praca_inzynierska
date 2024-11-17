<?php

use App\Http\Controllers\OrdersController;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\HandleInertiaRequests;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/orders', [OrdersController::class, 'index'])->name('orders');