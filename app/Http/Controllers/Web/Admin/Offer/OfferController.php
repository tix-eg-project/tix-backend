<?php

namespace App\Http\Controllers\Web\Admin\Offer;

use App\Http\Controllers\Controller;
use App\Http\Requests\Web\Offer\OfferRequest;
use App\Models\Offer;
use App\Models\Product;
use App\Models\Vendor;
use App\Services\Dashboard\OfferService;
use Illuminate\Http\Request;

class OfferController extends Controller
{
    protected $offerService;

    public function __construct(OfferService $offerService)
    {
        $this->offerService = $offerService;
    }

    public function index(Request $request)
    {
        $locale = app()->getLocale();
        $search = trim((string) $request->input('search', ''));

        $offers = Offer::query()
            ->with([
                'products:id,name',
                'vendor:id,name',
            ])
            ->when($search !== '', function ($q) use ($search, $locale) {
                $q->where(function ($qb) use ($search, $locale) {
                    $qb->where("name->$locale", 'like', "%{$search}%");
                });
            })
            ->when(
                $request->filled('vendor_id'),
                fn($q) =>
                $q->where('vendor_id', (int) $request->vendor_id)
            )
            ->when(
                $request->filled('amount_type'),
                fn($q) =>
                $q->where('amount_type', (int) $request->amount_type)
            )
            ->when(
                $request->filled('is_active'),
                fn($q) =>
                $q->where('is_active', (int) $request->is_active)
            )
            ->when(
                $request->filled('start_from'),
                fn($q) =>
                $q->whereDate('start_date', '>=', $request->start_from)
            )
            ->when(
                $request->filled('start_to'),
                fn($q) =>
                $q->whereDate('start_date', '<=', $request->start_to)
            )
            ->when(
                $request->filled('end_from'),
                fn($q) =>
                $q->whereDate('end_date', '>=', $request->end_from)
            )
            ->when(
                $request->filled('end_to'),
                fn($q) =>
                $q->whereDate('end_date', '<=', $request->end_to)
            )
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $vendors = Vendor::select('id', 'name')->orderBy('name')->get();

        return view('Admin.pages.offers.index', compact('offers', 'vendors'));
    }
    public function create()
    {
        $products = Product::all();
        $offer = null;
        return view('Admin.pages.offers.create', compact('products', 'offer'));
    }

    public function store(OfferRequest $request)
    {
        $data = $request->validated();
        $data['products'] = $request->input('products', []);
        $this->offerService->createOffer($data);
        return redirect()->route('offer.index')->with('success', 'Offer created successfully.');
    }

    public function edit(Offer $offer)
    {
        $products = Product::all();
        $offer->load('products');
        return view('Admin.pages.offers.update', compact('offer', 'products'));
    }

    public function update(OfferRequest $request, Offer $offer)
    {
        $data = $request->validated();
        if ($request->has('products')) {
            $data['products'] = $request->input('products', []);
        }
        $this->offerService->updateOffer($offer, $data);
        return redirect()->route('offer.index')->with('success', 'Offer updated successfully.');
    }

    public function destroy(Offer $offer)
    {
        $offer->delete();
        return redirect()->route('offer.index')->with('success', 'Offer deleted successfully.');
    }


    public function toggleStatus(Request $request, Offer $offer)
    {
        $offer->is_active = $request->is_active;
        $offer->save();

        return response()->json([
            'message' => $offer->is_active ? 'Offer activated successfully' : 'Offer deactivated successfully'
        ]);
    }
}
