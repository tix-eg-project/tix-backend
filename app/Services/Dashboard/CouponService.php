<?php

namespace App\Services\Dashboard;

use App\Models\Coupon;
use Illuminate\Support\Collection;

class CouponService
{
    public function all(): Collection
    {
        return Coupon::orderBy('created_at', 'desc')->get();
    }

    public function list(int $perPage = 10, ?string $search = null)
    {
        $query = Coupon::query()->latest();

        if (!empty($search)) {
            $query->where('code', 'like', '%' . $search . '%');
        }

        return $query->paginate($perPage);
    }

    public function find(int $id): ?Coupon
    {
        return Coupon::find($id);
    }

    public function create(array $data): Coupon
    {
        return Coupon::create($data);
    }

    public function store(array $data): Coupon
    {
        return $this->create($data);
    }

    public function update(Coupon $coupon, array $data): Coupon
    {
        $coupon->update($data);
        return $coupon;
    }

    public function delete(Coupon $coupon): void
    {
        $coupon->delete();
    }
}
