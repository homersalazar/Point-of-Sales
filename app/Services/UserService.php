<?php

namespace App\Services;

use App\Repositories\UserRepository;
use Illuminate\Http\Request;

class UserService extends BaseService
{
    protected $userRepo;

    public function __construct(UserRepository $userRepo)
    {
        parent::__construct($userRepo);
    }

    public function dataTable(Request $request)
    {
        return $this->repo->getUsersData($request);
    }

    public function login(array $credentials)
    {
        return $this->repo->login($credentials);
    }

    public function logout()
    {
        $this->repo->logout();
    }
}
