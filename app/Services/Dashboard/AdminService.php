<?php

namespace App\Services\Dashboard;

use App\Models\Admin;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class AdminService
{
    public function index()
    {
        return Admin::with('roles')->latest()->paginate(10);
    }

    public function store($data)
    {
        return DB::transaction(function () use ($data) {
            $data['password'] = Hash::make($data['password']);
            $admin = Admin::create($data);

            $roleName = Role::findOrFail($data['role'])->name;
            $admin->syncRoles([$roleName]);
            return $admin;
        });
    }

    public function update($admin, $data)
    {
        return DB::transaction(function () use ($admin, $data) {
            if (!empty($data['password'])) {
                $data['password'] = Hash::make($data['password']);
            } else {
                unset($data['password']);
            }
            $admin->update($data);

            $roleName = Role::findOrFail($data['role'])->name;
            $admin->syncRoles([$roleName]);
            return $admin;
        });
    }

    public function delete($admin)
    {
        if (auth('admin')->id() === $admin->id) {
            throw new \Exception(__('messages.cannot_delete_self'));
        }
        return $admin->delete();
    }

    public function roles()
    {
        return Role::pluck('name', 'id');
    }
}
