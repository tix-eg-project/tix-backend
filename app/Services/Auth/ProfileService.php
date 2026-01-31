<?php

namespace App\Services\Auth;

use App\Helpers\ImageManger;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class ProfileService
{

    public $imageManger;

    public function __construct(ImageManger $imageManger)
    {
        $this->imageManger = $imageManger;
    }
    public function me(): array
    {
        $u = Auth::user();

        // لو ImageManger فيه method اسمها url() هنستخدمها، وإلا هنستخدم asset() كـ fallback
        $imagePath = $u->image;
        $imageUrl  = $imagePath
            ? (method_exists($this->imageManger, 'url')
                ? $this->imageManger->url($imagePath)
                : asset($imagePath))
            : null;

        return [
            'id'         => $u->id,
            'name'       => $u->name,
            'email'      => $u->email,
            'phone'      => $u->phone,
            'address'    => $u->address,
            'image'      => $imageUrl,     // ← هنا بقى URL كامل زي: http://127.0.0.1:8000/users/...
            //'image_path' => $imagePath,    // (اختياري) لو حابب ترجع المسار الخام برضه
        ];
    }


    public function update(array $data): array
    {
        $user = Auth::user();

        if (!empty($data['password'])) {
            if (empty($data['current_password']) || !Hash::check($data['current_password'], $user->password)) {
                throw ValidationException::withMessages([
                    'current_password' => [__('auth.password')],
                ]);
            }
            $user->password = $data['password']; // هيتعمله hash تلقائي من الـ cast
        }

        $user->name    = $data['name']    ?? $user->name;
        $user->email   = $data['email']   ?? $user->email;
        $user->phone   = $data['phone']   ?? $user->phone;
        $user->address = $data['address'] ?? $user->address;

        if (!empty($data['image'])) {
            if (!empty($user->image)) {
                $this->imageManger->deleteImage($user->image);
            }
            $user->image = $this->imageManger->uploadImage('users', $data['image']); // بترجع "users/xxx.ext"
        }

        $user->save();

        return $this->me(); // ← هترجع URL كامل للصورة
    }
}
