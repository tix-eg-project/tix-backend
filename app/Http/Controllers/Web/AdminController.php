<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\ContactUs;
use App\Models\Country;
use App\Models\Order;
use App\Models\Product;
use App\Models\ShippingZone;
use App\Models\User;
use App\Models\Vendor;

class AdminController extends Controller
{
    // دالة لعرض صفحة الـ Dashboard
    public function dashboard()
    {
        $citycount = City::count();
        $countrycount = Country::count();
        $vendorcount = Vendor::count();
        $userscount = User::count();
        $ordercount = Order::count();
        $contactuscount = ContactUs::count();
        $shippingzone = ShippingZone::count();
        $productcount = Product::count();


        return view('Admin.dashboard', compact('vendorcount', 'citycount', 'countrycount', 'userscount', 'ordercount', 'contactuscount', 'productcount', 'shippingzone'));
    }

    // دالة لعرض صفحة الـ Tables
    public function tables()
    {
        return view('Admin.pages.tables');
    }

    public function billing()
    {
        return view('Admin.pages.billing');
    }

    public function virtualReality()
    {
        return view('Admin.pages.virtual-reality');
    }
}
