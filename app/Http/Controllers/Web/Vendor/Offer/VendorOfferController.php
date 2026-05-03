<?php

namespace App\Http\Controllers\Web\Vendor\Offer;

use App\Http\Controllers\Controller;
use App\Http\Requests\Web\Offer\OfferRequest;
use App\Models\Offer;
use App\Models\Product;
use App\Services\Dashboard\OfferService;
use Illuminate\Http\Request;

class VendorOfferController extends Controller
{
    public function __construct(private OfferService $offerService) {}

    private function currentVendorId(): int
    {
        return (int) auth('vendor')->id();
    }

    public function index(Request $request)
    {
        $vendorId   = $this->currentVendorId();
        $search     = trim((string) $request->input('search', ''));
        $amountType = $request->input('amount_type');
        $status     = $request->input('status');

        $offers = Offer::query()
            ->with(['products:id,name'])
            ->where('vendor_id', $vendorId)

            ->when($search !== '', function ($q) use ($search) {
                $loc = app()->getLocale();
                $q->where(function ($qq) use ($search, $loc) {
                    $qq->where("name->$loc", 'like', "%{$search}%")
                        ->orWhere('name->ar', 'like', "%{$search}%")
                        ->orWhere('name->en', 'like', "%{$search}%");
                });
            })

            ->when(in_array($amountType, ['1', '2'], true), function ($q) use ($amountType) {
                $q->where('amount_type', (int) $amountType); // 1=percent, 2=fixed
            })

            ->when($status === 'active',   fn($q) => $q->where('is_active', 1))
            ->when($status === 'inactive', fn($q) => $q->where('is_active', 0))

            ->latest('id')
            ->paginate(10)
            ->appends($request->query());

        return view('Vendor.pages.offers.index', compact('offers'));
    }


    public function create()
    {
        $products = Product::where('vendor_id', $this->currentVendorId())->latest()->get();
        $offer = null;
        return view('Vendor.pages.offers.create', compact('products', 'offer'));
    }

    public function store(OfferRequest $request)
    {
        $data = $request->validated();
        $data['products'] = $request->input('products', []);

        $this->offerService->createOffer($data);

        return redirect()->route('vendor.offers.index')->with('success', 'Offer created successfully.');
    }

    public function edit(Offer $offer)
    {
        if ((int)$offer->vendor_id !== $this->currentVendorId()) abort(403);

        $products = Product::where('vendor_id', $this->currentVendorId())->latest()->get();
        $offer->load('products');

        return view('Vendor.pages.offers.update', compact('offer', 'products'));
    }

    public function update(OfferRequest $request, Offer $offer)
    {
        if ((int)$offer->vendor_id !== $this->currentVendorId()) abort(403);

        $data = $request->validated();
        if ($request->has('products')) {
            $data['products'] = (array)$request->input('products', []);
        }

        $this->offerService->updateOffer($offer, $data);

        return redirect()->route('vendor.offers.index')->with('success', 'Offer updated successfully.');
    }

    public function destroy(Offer $offer)
    {
        if ((int)$offer->vendor_id !== $this->currentVendorId()) abort(403);

        $offer->delete();

        return redirect()->route('vendor.offers.index')->with('success', 'Offer deleted successfully.');
    }

    public function toggleStatus(Request $request, Offer $offer)
    {
        if ((int)$offer->vendor_id !== $this->currentVendorId()) abort(403);

        $offer->is_active = (int) $request->boolean('is_active');
        $offer->save();

        return response()->json([
            'message' => $offer->is_active ? 'Offer activated successfully' : 'Offer deactivated successfully'
        ]);
    }
}
