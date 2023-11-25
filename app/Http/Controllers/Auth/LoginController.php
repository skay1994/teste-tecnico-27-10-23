<?php

namespace App\Http\Controllers\Auth;

use App\Http\Requests\Auth\LoginRequest;
use App\Services\UserService;

class LoginController
{
    public function __construct(
        private readonly UserService $service
    )
    {
    }

    public function __invoke(LoginRequest $request)
    {
        return $this->service->login($request);
    }
}
