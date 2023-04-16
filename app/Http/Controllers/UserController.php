<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class UserController extends Controller
{
    /**
     * Get the authenticated User
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getUser()
    {
        $user = Auth::user();

        return response()->json([
            'user' => $user
        ]);
    }

    /**
     * Get a specific User by ID
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getUserById($id)
    {
        $user = User::find($id);

        if ($user) {
            return response()->json([
                'user' => $user
            ]);
        } else {
            return response()->json([
                'message' => 'User not found'
            ], 404);
        }
    }
}
