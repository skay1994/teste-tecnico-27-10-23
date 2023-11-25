<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;

class UserController extends Controller
{
    public function profile(): UserResource
    {
        return new UserResource(auth()->user());
    }
}
