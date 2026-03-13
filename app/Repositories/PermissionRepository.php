<?php

namespace App\Repositories;

use Spatie\Permission\Models\Permission;

class PermissionRepository extends BaseRepository
{
    public function __construct(Permission $model)
    {
        parent::__construct($model);
    }

    public function getAllPermissions()
    {
        return $this->model->select('id', 'name')
            ->where('name', 'LIKE', '%.%')
            ->where('parent_id', '!=', 0)
            ->groupBy('id', 'name')
            ->orderByRaw('name ASC')
            ->get();
    }

    public function sanitizePermissionName()
    {
        return $this->model->select('*')->where('name', 'NOT LIKE', '%.%')->get();
    }
}
