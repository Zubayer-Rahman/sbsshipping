<?php

namespace App\Http\Controllers;

use App\Models\User; // Import the User model
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        // Fetch only name and email from the users table
        $users = User::select('name', 'email')->get();

        // Send the data to a view
        return view('contacts.user', compact('users'));
    }
}