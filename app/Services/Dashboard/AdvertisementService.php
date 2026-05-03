<?php

namespace App\Services\Dashboard;

use App\Helpers\ImageManger;
use App\Models\Advertisement;


class AdvertisementService
{
    public $imageManger;

    public function __construct(ImageManger $imageManger)
    {
        $this->imageManger = $imageManger;
    }

    public function store(array $data): Advertisement
    {
        $data['image'] = $this->imageManger->uploadImage('advertisements', $data['image']);

        return Advertisement::create([
            'image' => $data['image'],
        ]);
    }

    public function delete(Advertisement $advertisement): bool
    {
        $this->imageManger->deleteImage($advertisement->image);

        return $advertisement->delete();
    }
}
