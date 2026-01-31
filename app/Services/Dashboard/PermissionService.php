<?php

namespace App\Services\Dashboard;

use Spatie\Permission\Models\Permission;
use Illuminate\Http\Request;

class PermissionService
{
    public function index(Request $request)
    {
        return Permission::when($request->search, function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%');
            })
            ->latest()
            ->paginate(10);
    }

    public function store(array $data)
    {
        return Permission::create($data);
    }

    public function find($id)
    {
        return Permission::findOrFail($id);
    }

    public function update($id, array $data)
    {
        $permission = $this->find($id);
        $permission->update($data);
        return $permission;
    }

    public function delete($id)
    {
        return $this->find($id)->delete();
    }
}
