<?php

namespace App\Http\Controllers\Web\Vendor\Product;

use App\Http\Controllers\Controller;
use App\Services\ProductReviewService;
use Illuminate\Http\Request;

class VendorProductReviewController extends Controller
{
    protected ProductReviewService $reviewService;

    public function __construct(ProductReviewService $reviewService)
    {
        $this->reviewService = $reviewService;
    }

    private function currentVendorId(): int
    {
        return auth('vendor')->id();
    }

    public function index(Request $request)
    {
        $productId = $request->integer('product_id');
        $reviews = $this->reviewService->getReviewsByVendor($this->currentVendorId(), $productId);

        return view('Vendor.pages.reviews.index', compact('reviews'));
    }

    public function toggleVisibility($id)
    {
        $this->reviewService->toggleVisibility($id, $this->currentVendorId());

        return redirect()->back()->with('success', __('messages.review_visibility_updated'));
    }
}
