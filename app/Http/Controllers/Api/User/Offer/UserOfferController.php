<?php

namespace App\Http\Controllers\Api\User\Offer;

use App\Helpers\ApiResponseHelper;
use App\Http\Controllers\Controller;
use App\Models\Offer;
use App\Models\Product;
use Illuminate\Http\JsonResponse;

class UserOfferController extends Controller
{
    public function index(): JsonResponse
    {
        $offers = Offer::latest()
            ->get()
            ->map(fn($offer) => [
                'id'          => $offer->id,

                'name'       => $offer->name,
                //'amount'     => $offer->amount,
                //'amount_type' => $offer->amount_type,


            ]);

        return ApiResponseHelper::success(
            __('messages.offer_list_retrieved'),
            $offers
        );
    }
}
