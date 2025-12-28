<?php

namespace App\Http\Controllers;

use App\Services\SupabaseService;
use Illuminate\Http\Request;

class UserController extends Controller
{
    protected $supabase;

    public function __construct(SupabaseService $supabase)
    {
        $this->supabase = $supabase;
    }

    public function index()
    {
        // Example: Get all users
        $users = $this->supabase->from('users')->select('*')->get();
        return response()->json($users);
    }

    public function show($id)
    {
        // Example: Get specific user
        $user = $this->supabase->from('users')->select('*')->eq('id', $id)->get();
        return response()->json($user);
    }

    public function store(Request $request)
    {
        // Example: Create a new user
        $userData = $request->validate([
            'name' => 'required|string',
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ]);

        $user = $this->supabase->from('users')->insert([
            'name' => $userData['name'],
            'email' => $userData['email'],
            'password' => bcrypt($userData['password']),
        ]);

        return response()->json($user, 201);
    }
}