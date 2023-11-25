<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Services\UserService;

class RegisterController extends Controller
{
    public function __construct(
        private readonly UserService $service
    )
    {
    }

    public function __invoke(RegisterRequest $request)
    {
        return $this->service->register($request);
    }
}
