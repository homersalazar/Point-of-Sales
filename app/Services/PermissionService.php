<?php

namespace App\Services;

use App\Repositories\PermissionRepository;

class PermissionService extends BaseService
{
    protected $permissionRepo;

    public function __construct(PermissionRepository $permissionRepo)
    {
        $this->permissionRepo = $permissionRepo;
        parent::__construct($permissionRepo);
    }

    public function getAllPermissions()
    {
        return $this->permissionRepo->getAllPermissions();
    }

    public function sanitizePermissionName()
    {
        return $this->permissionRepo->sanitizePermissionName();
    }
}
