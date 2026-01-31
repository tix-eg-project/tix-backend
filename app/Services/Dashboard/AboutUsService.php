<?php

namespace App\Services\Dashboard;

use App\Models\AboutUs;
use App\Helpers\ImageManger;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class AboutUsService
{
    public function __construct(private ImageManger $images) {}

    public function store(array $data): AboutUs
    {
        return $this->save($data);
    }

    public function update(AboutUs $about, array $data): AboutUs
    {
        return $this->save($data);
    }

    private function save(array $data): AboutUs
    {
        return DB::transaction(function () use ($data) {
            $payload = Arr::only($data, ['title', 'description']);

            $before = AboutUs::first();
            $about  = AboutUs::updateOrCreate([], $payload);

            if (!empty($data['image'])) {
                if (!empty($before?->image)) {
                    $this->images->deleteImage($before->image);
                }
                $path = $this->images->uploadImage('aboutus', $data['image']);
                $about->update(['image' => $path]);
            }

            return $about;
        });
    }
}
