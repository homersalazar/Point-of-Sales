<?php

namespace App\Repositories;

use Spatie\Permission\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RoleRepository extends BaseRepository
{
    public function __construct(Role $model)
    {
        parent::__construct($model);
    }

    public function getRoleData(Request $request)
    {
        $columns = ['name', 'action'];

        $totalData = DB::table('roles')->count();
        $totalFiltered = $totalData;

        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');

        $query = DB::table('roles');

        if (!empty($request->input('search.value'))) {

            $search = $request->input('search.value');

            $query->where('name', 'LIKE', "%{$search}%");

            $totalFiltered = $query->count();
        }

        $roles = $query->offset($start)
            ->limit($limit)
            ->orderBy($order, $dir)
            ->get();

        $data = [];

        foreach ($roles as $row) {

            $action = '';
            $action = '<div class="flex gap-2">
                            <button
                                type="button"
                                onclick="update_role(\'' . $row->id . '\', \'' . $row->name . '\')"
                                class="btn btn-info btn-outline btn-sm"
                            >
                                <i class="fa fa-edit"></i>
                            </button>

                            <a href="/access/permissionByRole/' . $row->id . '" class="btn btn-warning btn-outline btn-sm">
                                <i class="fa fa-user-shield"></i>
                            </a>
                        </div>';
            $data[] = [
                'id' => $row->id,
                'name' => $row->name,
                'action' => $action
            ];
        }

        return [
            "draw" => intval($request->input('draw')),
            "recordsTotal" => $totalData,
            "recordsFiltered" => $totalFiltered,
            "data" => $data
        ];
    }

    public function getRoleById($id)
    {
        return $this->model->with('permissions')->findOrFail($id);
    }
}
