<?php

namespace App\Services\Dashboard\Vendor;

use App\Helpers\ImageManger;
use App\Models\Admin;
use App\Models\Vendor;
use App\Notifications\DashboardNotification;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;


class VendorService
{
    protected $imageManger;

    public function __construct(ImageManger $imageManger)
    {
        $this->imageManger = $imageManger;
    }


    public function store(array $data): Vendor
    {
        $data['password'] = Hash::make($data['password']);

        $profilePath = !empty($data['image'])
            ? $this->imageManger->uploadImage('vendors', $data['image'])
            : null;

        $frontPath = !empty($data['id_card_front_image'])
            ? $this->imageManger->uploadImage('vendors/id_cards', $data['id_card_front_image'])
            : null;

        $backPath = !empty($data['id_card_back_image'])
            ? $this->imageManger->uploadImage('vendors/id_cards', $data['id_card_back_image'])
            : null;

        $vendor = Vendor::create([
            'company_name'          => $data['company_name'],
            'description'           => $data['description'],
            'name'                  => $data['name'],
            'email'                 => $data['email'],
            'phone'                 => $data['phone'],
            'password'              => $data['password'],
            'image'                 => $profilePath,
            'address'               => $data['address'],
            'postal_code'           => $data['postal_code'],
            'vodafone_cash'         => $data['vodafone_cash'],
            'instapay'              => $data['instapay'],
            'type_business'         => $data['type_business'],
            'category_id'           => $data['category_id'],
            'country_id'            => $data['country_id'],
            'city_id'               => $data['city_id'],
            'id_card_front_image'   => $frontPath,
            'id_card_back_image'    => $backPath,
        ]);

        $admins = Admin::all();
        foreach ($admins as $admin) {
            Log::info('Sending notification to admin ID: ' . $admin->id);
            $admin->notify(new DashboardNotification('تم تسجيل بائع جديد: ' . $vendor->name . ' - ' . $vendor->email));
        }

        return $vendor;
    }


    public function update(Vendor $vendor, array $data): Vendor
    {
        // نسمح بالباسورد كمان ضمن الحقول
        $allowed = Arr::only($data, ['name', 'description', 'image', 'password']);
    
        // الصورة كما هي
        if (!empty($allowed['image'])) {
            if (!empty($vendor->image)) {
                $this->imageManger->deleteImage($vendor->image);
            }
            $allowed['image'] = $this->imageManger->uploadImage('vendors', $allowed['image']);
        }
    
        // نجهز مصفوفة التحديث الأساسية
        $update = [
            'name'        => array_key_exists('name', $allowed) ? $allowed['name'] : $vendor->name,
            'description' => array_key_exists('description', $allowed) ? $allowed['description'] : $vendor->description,
            'image'       => array_key_exists('image', $allowed) ? $allowed['image'] : $vendor->image,
        ];
    
        // لو اتبعت باسورد غير فاضي: نعمله hash ونضيفه للتحديث
        if (!empty($allowed['password'])) {
            $update['password'] = Hash::make($allowed['password']);
        }
    
        $vendor->update($update);
    
        return $vendor->refresh();
    }

    public function getById(int $id): Vendor
    {
        return Vendor::findOrFail($id);
    }
}
