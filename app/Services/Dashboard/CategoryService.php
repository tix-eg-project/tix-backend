<?php

namespace App\Services\Dashboard;

use App\Helpers\ImageManger;
use App\Models\Category;


class CategoryService
{
    protected $imageManger;
    public function __construct(ImageManger $imageManger)
    {
        $this->imageManger = $imageManger;
    }


    public function store(array $data): Category
    {

        $data['image'] = $this->imageManger->uploadImage('categories', $data['image']);

        $category = Category::create(
            [
                'name' => $data['name'],
                'image' => $data['image'],
            ]

        );

        return $category;
    }


    public function update(Category $category, array $data): Category
    {

        $category->update([
            'name' => $data['name'],
        ]);
        if (!empty($data['image'])) {

            if ($category->image) {
            }


            $category->image = $this->imageManger->uploadImage('categories', $data['image']);
        }

        $category->save();

        return $category;
    }

    public function delete(Category $category): bool
    {
        $this->imageManger->deleteImage($category->image);
        return $category->delete();
    }

    public function getCategories()
    {
        return Category::all();
    }
}
