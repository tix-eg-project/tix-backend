<?php

namespace App\Services\Dashboard\Product;

use App\Models\Product;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\App;



class SearchService
{
    public function searchProducts(?string $query, int $perPage = 16): LengthAwarePaginator
    {
        $q = Product::query()
            ->with([
                'brand:id,name',
                'subcategory:id,name,category_id',
                'subcategory.category:id,name',
            ]);



        if ($query) {
            $like = "%" . str_replace(['%', '_'], ['\\%', '\\_'], $query) . "%";

            $q->where(function (Builder $w) use ($like) {
                $w->where(function (Builder $x) use ($like) {
                    $x->orWhereRaw("LOWER(JSON_UNQUOTE(JSON_EXTRACT(name, '$.ar'))) LIKE LOWER(?)", [$like])
                        ->orWhereRaw("LOWER(JSON_UNQUOTE(JSON_EXTRACT(name, '$.en'))) LIKE LOWER(?)", [$like])
                        ->orWhere('name', 'LIKE', $like);
                })
                    ->orWhere(function (Builder $x) use ($like) {
                        $x->orWhereRaw("LOWER(JSON_UNQUOTE(JSON_EXTRACT(short_description, '$.ar'))) LIKE LOWER(?)", [$like])
                            ->orWhereRaw("LOWER(JSON_UNQUOTE(JSON_EXTRACT(short_description, '$.en'))) LIKE LOWER(?)", [$like])
                            ->orWhere('short_description', 'LIKE', $like);
                    })
                    ->orWhere(function (Builder $x) use ($like) {
                        $x->orWhereRaw("LOWER(JSON_UNQUOTE(JSON_EXTRACT(long_description, '$.ar'))) LIKE LOWER(?)", [$like])
                            ->orWhereRaw("LOWER(JSON_UNQUOTE(JSON_EXTRACT(long_description, '$.en'))) LIKE LOWER(?)", [$like])
                            ->orWhere('long_description', 'LIKE', $like);
                    });
            });
        } else {
            $q->orderByDesc('id');
        }
        return $q->paginate($perPage)->withQueryString();
    }
}
