<?php

namespace App\Services\Dashboard;

use App\Helpers\ImageManger;
use App\Models\Subcategory;

class SubCategoryService
{
    protected $imageManger;
    public function __construct(ImageManger $imageManger)
    {
        $this->imageManger = $imageManger;
    }

    public function store(array $data): Subcategory
    {
        $data['image'] = $this->imageManger->uploadImage('subcategories', $data['image']);

        return Subcategory::create([
            'name' => $data['name'],
            'description' => $data['description'],
            'category_id' => $data['category_id'],
            'image' => $data['image'],
        ]);
    }

    public function update(Subcategory $subcategory, array $data): Subcategory
    {

        if (isset($data['category_id'])) {
            $subcategory->category_id = $data['category_id'];
        }


        $subcategory->name = $data['name'];
        $subcategory->description = $data['description'];

        if (!empty($data['image'])) {

            if ($subcategory->image) {
            }


            $subcategory->image = $this->imageManger->uploadImage('subcategories', $data['image']);
        }

        $subcategory->save();

        return $subcategory;
    }


    public function delete(Subcategory $subcategory): bool
    {
        $this->imageManger->deleteImage($subcategory->image);
        return $subcategory->delete();
    }
    public function getSubCategories()
    {
        return Subcategory::all();
    }
}
