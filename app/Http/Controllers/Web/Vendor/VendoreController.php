<?php

namespace App\Http\Controllers\Web\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Vendor;
use App\Services\Dashboard\VendoreService;
use Illuminate\Http\Request;

class VendoreController extends Controller
{
    public function __construct(private readonly VendoreService $vendoreService) {}

    public function index()
    {
        $vendors = $this->vendoreService->index();
        return view('Admin.pages.vendors.index', compact('vendors'));
    }

    public function show(string $id)
    {
        $vendor = $this->vendoreService->show($id);
        return view('Admin.pages.vendors.showDetails', compact('vendor'));
    }

    public function updateStatus(Request $request, Vendor $vendor)
    {
        $request->validate([
            'status' => 'required|in:0,1',
        ]);

        // الميل بقى جوّه السيرفيس
        $this->vendoreService->updateStatus($request->status, $vendor);

        return redirect()->back()->with('success', 'تم تحديث حالة الاشتراك بنجاح');
    }

    public function destroy(string $id)
    {
        $this->vendoreService->destroy($id);
        return redirect()->route('vendore.index')->with('success', 'Vendor Deleted Successfully.');
    }
}
