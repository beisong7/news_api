<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UserController extends Controller
{
    public function profile(Request $request){
        $user = $request->user();

        return $this->successResponse($user, "user profile loaded");

    }
}
