<?php

namespace App\Http\Controllers\Api\Country\Cities;

use App\Helpers\ApiResponseHelper;
use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\Country;
use App\Services\Dashboard\CityService;
use App\Services\Dashboard\CountryService;
use Illuminate\Http\Request;

class CityController extends Controller
{
    public function index(Request $request, $country_id)
    {

        $country = Country::find($country_id);

        if (!$country) {
            return ApiResponseHelper::error('messages.country_not_found');
        }


        $cities = City::where('country_id', $country_id)->select('id', 'name')->get();

        return ApiResponseHelper::success('messages.success', $cities);
    }
}
