<?php

namespace App\Services\Dashboard;

use App\Models\Offer;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OfferService
{
    private function vendorContextId(): ?int
    {
        return (request()->routeIs('vendor.*') && Auth::guard('vendor')->check())
            ? (int) Auth::guard('vendor')->id()
            : null;
    }

    public function createOffer(array $data): Offer
    {
        return DB::transaction(function () use ($data) {

            $vendorId = $this->vendorContextId();

            $data['vendor_id'] = $vendorId ?? null;

            $shouldSync = array_key_exists('products', $data);
            $products   = $shouldSync ? (array)($data['products'] ?? []) : [];
            unset($data['products']);

            if ($vendorId) {
                $allowed = Product::query()
                    ->where('vendor_id', $vendorId)
                    ->whereIn('id', $products)
                    ->pluck('id')
                    ->all();
                $products = $allowed;
            }

            $offer = Offer::create($data);

            if ($shouldSync) {
                $offer->products()->sync($products);
            }

            return $offer;
        });
    }

    public function updateOffer(Offer $offer, array $data): Offer
    {
        return DB::transaction(function () use ($offer, $data) {

            $vendorId = $this->vendorContextId();

            unset($data['vendor_id']);

            $shouldSync = array_key_exists('products', $data);
            $products   = $shouldSync ? (array)($data['products'] ?? []) : [];
            unset($data['products']);

            if ($vendorId) {
                if ((int)$offer->vendor_id !== $vendorId) {
                    abort(403);
                }
                $allowed = Product::query()
                    ->where('vendor_id', $vendorId)
                    ->whereIn('id', $products)
                    ->pluck('id')
                    ->all();
                $products = $allowed;
            }

            $offer->update($data);

            if ($shouldSync) {
                $offer->products()->sync($products);
            }

            return $offer;
        });
    }
}
