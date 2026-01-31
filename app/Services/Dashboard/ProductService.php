<?php

namespace App\Services\Dashboard;

use App\Enums\Status;
use App\Enums\AmountType;
use App\Helpers\ImageManger;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ProductService
{
    protected ImageManger $imageManger;

    public function __construct(ImageManger $imageManger)
    {
        $this->imageManger = $imageManger;
    }

    /** في سياق الفندور فقط (route name يبدأ بـ vendor.) */
    private function vendorContextId(): ?int
    {
        return (request()->routeIs('vendor.*') && Auth::guard('vendor')->check())
            ? (int) Auth::guard('vendor')->id()
            : null;
    }

    public function store(array $data): Product
    {
        $data['images'] = !empty($data['images'])
            ? $this->imageManger->uploadMultiImage('products', (array) $data['images'])
            : [];

        $vendorId = $this->vendorContextId();

        $payload = [
            'name'              => $data['name'],
            'short_description' => $data['short_description'] ?? null,
            'long_description'  => $data['long_description'] ?? null,
            'price'             => $data['price'],
            'discount'          => $data['discount'] ?? 0,
            'discount_type'     => $data['discount_type'] ?? AmountType::fixed,
            'quantity'          => $data['quantity'] ?? 0,
            'images'            => $data['images'],
            'category_id'       => $data['category_id'],
            'subcategory_id'    => $data['subcategory_id'] ?? ($data['sub_category_id'] ?? null),
            'brand_id'          => $data['brand_id'] ?? null,

            // 🔒 لو مش سياق فندور => دايمًا null (حتى لو حد بعت vendor_id في الريكوست)
            'vendor_id'         => $vendorId ?? null,

            'status'            => $data['status'] ?? Status::Active,
        ];

        return Product::create($payload);
    }

    public function update(Product $product, array $data): Product
    {
        $currentImages = (array) ($product->images ?? []);

        if (!empty($data['remove_images']) && is_array($data['remove_images'])) {
            foreach ($data['remove_images'] as $img) {
                Storage::disk('public')->delete($img);
            }
            $currentImages = array_values(array_diff($currentImages, $data['remove_images']));
        }

        if (!empty($data['images'])) {
            $new = $this->imageManger->uploadMultiImage('products', (array) $data['images']);

            if (!empty($data['replace_images'])) {
                foreach ($currentImages as $img) {
                    Storage::disk('public')->delete($img);
                }
                $currentImages = $new;
            } else {
                $currentImages = array_values(array_unique(array_merge($currentImages, $new)));
            }
        }

        // في التحديث: لا نسمح بتغيير vendor_id لا من الفندور ولا من الأدمن
        // (لو حبيت تسمح للأدمن لاحقًا، نضبطها ساعتها)
        $payload = [
            'name'              => $data['name']              ?? $product->name,
            'short_description' => $data['short_description'] ?? $product->short_description,
            'long_description'  => $data['long_description']  ?? $product->long_description,
            'price'             => $data['price']             ?? $product->price,
            'discount'          => $data['discount']          ?? $product->discount,
            'discount_type'     => $data['discount_type']     ?? $product->discount_type,
            'quantity'          => $data['quantity']          ?? $product->quantity,
            'images'            => $currentImages,
            'category_id'       => $data['category_id']       ?? $product->category_id,
            'subcategory_id'    => $data['subcategory_id']    ?? ($data['sub_category_id'] ?? $product->subcategory_id),
            'brand_id'          => $data['brand_id']          ?? $product->brand_id,
            'vendor_id'         => $product->vendor_id, // 🔒 ثابت
            'status'            => $data['status']            ?? $product->status,
        ];

        $product->update($payload);

        return $product->refresh();
    }

    public function delete(Product $product): bool
    {
        foreach ((array) ($product->images ?? []) as $img) {
            Storage::disk('public')->delete($img);
        }
        return (bool) $product->delete();
    }
}
