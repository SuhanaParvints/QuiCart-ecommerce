<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;

class UserController extends Controller
{
    public function index()
    {
        return User::latest()->get();
    }

    public function destroy($id)
    {
        User::findOrFail($id)->delete();

        return response()->json([
            'message' => 'User deleted successfully'
        ]);
    }
}