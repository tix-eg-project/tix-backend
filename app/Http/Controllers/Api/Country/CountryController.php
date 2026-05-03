<?php

namespace App\Http\Controllers\Api\Country;

use App\Helpers\ApiResponseHelper;
use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Services\Dashboard\CityService;
use App\Services\Dashboard\CountryService;
use Illuminate\Http\Request;

class CountryController extends Controller
{
    public function index()
    {

        $countries = Country::select('id', 'name')->get();


        return ApiResponseHelper::success('messages.success', $countries);
    }
}
