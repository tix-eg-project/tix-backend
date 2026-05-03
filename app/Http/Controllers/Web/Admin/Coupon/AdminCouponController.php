<?php

namespace App\Http\Controllers\Web\Admin\Coupon;

use App\Http\Controllers\Controller;
use App\Http\Requests\Web\Admin\Coupon\StoreCouponRequest;
use App\Http\Requests\Web\Admin\Coupon\UpdateCouponRequest;
use App\Models\Coupon;
use App\Services\Dashboard\CouponService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Redirect;

class AdminCouponController extends Controller
{
    protected CouponService $couponService;

    public function __construct(CouponService $couponService)
    {
        $this->couponService = $couponService;
    }

    public function index(Request $request)
    {
        $search        = trim((string) $request->input('search', ''));
        $discountType  = $request->input('discount_type');
        $state         = $request->input('state');
        $startFrom     = $request->input('start_from');
        $endTo         = $request->input('end_to');
        $now           = now();

        $coupons = Coupon::query()
            ->when($search !== '', function ($q) use ($search) {
                $q->where(function ($qq) use ($search) {
                    $qq->where('code', 'like', "%{$search}%");
                });
            })
            ->when(in_array($discountType, ['percent', 'amount'], true), function ($q) use ($discountType) {
                $q->where('discount_type', $discountType);
            })
            ->when($state, function ($q) use ($state, $now) {
                if ($state === 'disabled') {
                    $q->where('is_active', 0);
                } elseif ($state === 'scheduled') {
                    $q->where('is_active', 1)->whereNotNull('starts_at')->where('starts_at', '>', $now);
                } elseif ($state === 'expired') {
                    $q->whereNotNull('ends_at')->where('ends_at', '<', $now);
                } elseif ($state === 'active') {
                    $q->where('is_active', 1)
                        ->where(function ($qq) use ($now) {
                            $qq->whereNull('starts_at')->orWhere('starts_at', '<=', $now);
                        })
                        ->where(function ($qq) use ($now) {
                            $qq->whereNull('ends_at')->orWhere('ends_at', '>=', $now);
                        });
                }
            })
            ->when($startFrom, function ($q) use ($startFrom) {
                $q->whereDate('starts_at', '>=', $startFrom);
            })
            ->when($endTo, function ($q) use ($endTo) {
                $q->whereDate('ends_at', '<=', $endTo);
            })
            ->latest('id')
            ->paginate(20)
            ->appends($request->query());

        return view('Admin.pages.coupons.index', compact('coupons'));
    }


    public function create()
    {
        return View::make('Admin.pages.coupons.create');
    }

    public function store(StoreCouponRequest $request)
    {
        $this->couponService->store($request->validated());

        return Redirect::route('admin.coupons.index')->with('success', __('messages.coupon_created'));
    }

    public function edit(Coupon $coupon)
    {
        return View::make('Admin.pages.coupons.edit', compact('coupon'));
    }

    public function update(UpdateCouponRequest $request, Coupon $coupon)
    {
        $this->couponService->update($coupon, $request->validated());

        return Redirect::route('admin.coupons.index')->with('success', __('messages.coupon_updated'));
    }

    public function destroy(Coupon $coupon)
    {
        $this->couponService->delete($coupon);

        return Redirect::route('admin.coupons.index')->with('success', __('messages.coupon_deleted'));
    }
}
