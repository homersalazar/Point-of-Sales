<?php

namespace App\Repositories;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserRepository extends BaseRepository
{
    public function __construct(User $model)
    {
        parent::__construct($model);
    }

    public function getUsersData(Request $request)
    {
        $columns = ['users.name', 'users.email', 'roles.name', 'action'];

        $totalData = DB::table('users')->count();

        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');

        $query = DB::table('users')
            ->leftJoin('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
            ->leftJoin('roles', 'model_has_roles.role_id', '=', 'roles.id')
            ->select('users.id', 'users.name', 'users.email', DB::raw('COALESCE(roles.name, "") as role_name'));

        if (!empty($search = $request->input('search.value'))) {
            $query->where('users.name', 'LIKE', "%{$search}%")
                ->orWhere('users.email', 'LIKE', "%{$search}%")
                ->orWhere('roles.name', 'LIKE', "%{$search}%");
        }

        $totalFiltered = (clone $query)->count();

        $users = $query->offset($start)
            ->limit($limit)
            ->orderBy($order, $dir)
            ->get();

        $data = [];
        foreach ($users as $row) {
            $action = '<div class="flex gap-2">
                <button type="button" onclick="update_user(\'' . $row->id . '\', \'' . $row->name . '\', \'' . $row->email . '\')" class="btn btn-info btn-outline btn-sm">
                    <i class="fa fa-edit"></i>
                </button>
                <a href="/user/assign_access/' . $row->id . '" class="btn btn-warning btn-outline btn-sm">
                    <i class="fa fa-user-shield"></i>
                </a>
            </div>';

            $data[] = [
                'id' => $row->id,
                'name' => $row->name,
                'email' => $row->email,
                'role' => $row->role_name,
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

    public function login(array $credentials)
    {
        $user = User::where('email', $credentials['email'])->first();

        if ($user && Hash::check($credentials['password'], $user->password)) {

            // Simulate token generation
            $token = bin2hex(random_bytes(16));

            return [
                'success' => true,
                'token' => $token,
                'user' => $user
            ];
        }

        return [
            'success' => false,
            'message' => 'Invalid credentials'
        ];
    }

    public function logout()
    {
        // Simulate token invalidation
        return true;
    }
}
