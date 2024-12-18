<?php

use Illuminate\Support\Facades\Route;

use Inertia\Inertia;
use App\Models\User;


Route::get('/customers', function () {
    $customers = User::orderBy('name')->get();
    return Inertia::render('Customers', ['customers' => $customers]);
});
