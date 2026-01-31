<?php

namespace App\Services\Dashboard;

use App\Models\Banner;

use App\Helpers\ImageManger;

class BannerService
{
    public $imageManger;

    public function __construct(ImageManger $imageManger)
    {
        $this->imageManger = $imageManger;
    }

    public function store(array $data): Banner
    {

        $data['image'] = $this->imageManger->uploadImage('banners', $data['image']);

        $banner = Banner::create(
            [
                'title' => $data['title'],
                'description' => $data['description'],
                'vendor_id' => $data['vendor_id'],
                'image' => $data['image'],
            ]
        );

        return $banner;
    }

    public function update(Banner $banner, array $data): Banner
    {
        $banner->update([
            'title' => $data['title'],
            'description' => $data['description'],

            'vendor_id' => $data['vendor_id'],

        ]);



        if (!empty($data['image'])) {

            if ($banner->image) {
            }


            $banner->image = $this->imageManger->uploadImage('banners', $data['image']);
        }


        $banner->save();

        return $banner;
    }

    public function delete(Banner $banner): bool
    {

        if ($banner->image) {
            $this->imageManger->deleteImage($banner->image);  // حذف الصورة
        }

        return $banner->delete();
    }
}
