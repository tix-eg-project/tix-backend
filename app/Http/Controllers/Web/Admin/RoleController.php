<?php


namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Web\Admin\Role\StoreRoleRequest;
use App\Http\Requests\Web\Admin\Role\UpdateRoleRequest;
use App\Services\Dashboard\RoleService;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    public function __construct(protected RoleService $service) {}

    public function index(Request $request)
    {
        $roles = $this->service->index($request);
        return view('Admin.pages.roles.index', compact('roles'));
    }

    public function create()
    {
        $permissions = $this->service->allPermissions();
        return view('Admin.pages.roles.create', compact('permissions'));
    }

    public function store(StoreRoleRequest $request)
    {
        $this->service->store($request->validated());
        return redirect()->route('admin.roles.index')->with('success', __('messages.created_successfully'));
    }

    public function edit($id)
    {
        $role = $this->service->find($id);
        $permissions = $this->service->allPermissions();
        return view('Admin.pages.roles.edit', compact('role', 'permissions'));
    }

    public function update(UpdateRoleRequest $request, $id)
    {
        $this->service->update($request->validated(), $id);
        return redirect()->route('admin.roles.index')->with('success', __('messages.updated_successfully'));
    }

    public function destroy($id)
    {
        $this->service->delete($id);
        return redirect()->back()->with('success', __('messages.deleted_successfully'));
    }
}
