<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CustomerController;


Route::prefix('customers')->group(function () {
    Route::get('/', [CustomerController::class, 'index']);
    Route::get('/load-more', [CustomerController::class, 'getClientsByLetter']);
});
