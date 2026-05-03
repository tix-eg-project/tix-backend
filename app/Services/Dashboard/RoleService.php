<?php

namespace App\Services\Dashboard;

use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\DB;

class RoleService
{
    public function index($request)
    {
        return Role::when($request->search, function ($q) use ($request) {
            $q->where('name', 'like', '%' . $request->search . '%');
        })
            ->with('permissions')
            ->latest()
            ->paginate(10);
    }

    public function store($data)
    {
        return DB::transaction(function () use ($data) {
            $role = Role::create([
                'name' => $data['name'],
                'guard_name' => 'admin',
            ]);

            $permissionNames = Permission::whereIn('id', $data['permissions'])->pluck('name')->toArray();
            $role->syncPermissions($permissionNames);

            return $role;
        });
    }

    public function find($id)
    {
        return Role::with('permissions')->findOrFail($id);
    }

    public function update($data, $id)
    {
        return DB::transaction(function () use ($data, $id) {
            $role = $this->find($id);
            $role->update(['name' => $data['name']]);

            $permissionNames = Permission::whereIn('id', $data['permissions'])->pluck('name')->toArray();
            $role->syncPermissions($permissionNames);

            return $role;
        });
    }

    public function delete($id)
    {
        $role = $this->find($id);
        return $role->delete();
    }

    public function allPermissions()
    {
        return Permission::all();
    }
}
