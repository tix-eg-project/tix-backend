<?php

namespace App\Helpers;

use Illuminate\Support\Str;


class ImageManger
{

    public function uploadImage($path, $image, $disk = 'public')
    {
        $file_name = $this->generateImageName($image);
        Self::storeImageInLocale($image, $path, $file_name, $disk);
        return $path . '/' . $file_name;
    }

    public function generateImageName($image)
    {
        $file_name = time() . '_' . uniqid() . '_' . $image->getClientOriginalExtension();
        return $file_name;
    }

    public function storeImageInLocale($image, $path, $file_name, $disk)
    {
        $image->storePubliclyAs($path, $file_name, $disk);
    }

    public function deleteImage($image): void
    {
        if (file_exists(public_path($image))) {
            unlink(public_path($image));
        }
    }


    public function uploadMultiImage($path, $images, $disk = 'public')
    {
        $imagePaths = [];
        foreach ($images as $image) {
            $imageName = $this->generateImageName($image);
            $this->storeImageInLocale($image, $path, $imageName, $disk);
            $imagePaths[] = $path . '/' . $imageName;
        }
        return $imagePaths;
    }   // ============== END METHOD ===========





}
