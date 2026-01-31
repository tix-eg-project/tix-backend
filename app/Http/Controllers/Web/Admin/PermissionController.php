<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Web\Admin\Permission\StorePermissionRequest;
use App\Http\Requests\Web\Admin\Permission\UpdatePermissionRequest;
use App\Services\Dashboard\PermissionService;
use Illuminate\Http\Request;

class PermissionController extends Controller
{
    public function __construct(protected PermissionService $service) {}

    public function index(Request $request)
    {
        return view('Admin.pages.permissions.index', [
            'permissions' => $this->service->index($request)
        ]);
    }

    public function create()
    {
        return view('Admin.pages.permissions.create');
    }

    public function store(StorePermissionRequest $request)
    {
        $this->service->store($request->validated());
        return redirect()->route('admin.permissions.index')->with('success', __('messages.created_successfully'));
    }

    public function edit($id)
    {
        $permission = $this->service->find($id);
        return view('Admin.pages.permissions.edit', compact('permission'));
    }

    public function update(UpdatePermissionRequest $request, $id)
    {
        $this->service->update($id, $request->validated());
        return redirect()->route('admin.permissions.index')->with('success', __('messages.updated_successfully'));
    }

    public function destroy($id)
    {
        $this->service->delete($id);
        return redirect()->back()->with('success', __('messages.deleted_successfully'));
    }
}
