<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\NameService;
use App\Services\PasswordService;
use Illuminate\Http\Request;

class UserChangeController extends Controller
{
    public function __construct(
        protected PasswordService $passwordService,
        protected NameService $nameService,
    ) {}


    public function changeUserPassword(Request $request)
    {
        return $this->passwordService->changePassword($request['current_password']);
    }

    public function changeName(Request $request)
    {
        return $this->nameService->changeName($request['name']);
    }
}
