<?php

namespace App\Services;

use App\Repositories\RoleRepository;
use Illuminate\Http\Request;

class RoleService extends BaseService
{
    protected $roleRepo;

    public function __construct(RoleRepository $roleRepo)
    {
        parent::__construct($roleRepo);
    }

    public function dataTable(Request $request)
    {
        return $this->repo->getRoleData($request);
    }

    public function getRoleById($id)
    {
        return $this->repo->getRoleById($id);
    }
}
