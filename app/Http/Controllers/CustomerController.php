<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Inertia\Inertia;

class CustomerController extends Controller
{
    public function index()
    {
        return Inertia::render('Customers');
    }

    // Laravel Controller Example
    public function getClientsByLetter(Request $request)
    {
        $letter = strtoupper($request->get('letter', 'A')); // Get the letter from the query parameter

        $users = User::orderBy('name')->where('name', 'like', $letter . '%')->get();

        return response()->json($users);
    }
}
