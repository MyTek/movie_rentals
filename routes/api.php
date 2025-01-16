<?php

use App\Http\Controllers\MovieController;
use App\Http\Controllers\OrderController;
use Illuminate\Support\Facades\Route;

Route::apiResource('movies', MovieController::class);

Route::apiResource('orders', OrderController::class);
