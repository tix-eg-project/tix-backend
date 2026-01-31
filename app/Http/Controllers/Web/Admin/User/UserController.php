<?php

namespace App\Http\Controllers\Web\Admin\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\Web\Admin\UpdateProfileRequest;
use App\Http\Requests\Web\Admin\User\StoreAdminRequest;
use App\Http\Requests\Web\Admin\User\UpdateAdminRequest;
use App\Models\Admin;
use App\Services\Dashboard\AdminProfileService;
use App\Services\Dashboard\AdminService;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function __construct(private AdminService $adminService) {}

    public function index(Request $request)
    {
        $admins = $this->adminService->index();
        return view('Admin.pages.admins.index', compact('admins'));
    }

    public function create()
    {
        $roles = $this->adminService->roles();
        return view('Admin.pages.admins.create', compact('roles'));
    }

    public function store(StoreAdminRequest $request)
    {
        $this->adminService->store($request->validated());
        return redirect()->route('admin.admins.index')->with('success', __('messages.added_successfully'));
    }

    public function edit(Admin $admin)
    {
        $roles = $this->adminService->roles();
        return view('Admin.pages.admins.edit', compact('admin', 'roles'));
    }

    public function update(UpdateAdminRequest $request, Admin $admin)
    {
        $this->adminService->update($admin, $request->validated());
        return redirect()->route('admin.admins.index')->with('success', __('messages.updated_successfully'));
    }

    public function destroy(Admin $admin)
    {
        try {
            $this->adminService->delete($admin);
            return redirect()->back()->with('success', __('messages.deleted_successfully'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
