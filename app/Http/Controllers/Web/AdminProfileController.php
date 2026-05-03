<?php

namespace App\Http\Controllers\Web;

use App\Helpers\ImageManger;
use App\Http\Controllers\Controller;
use App\Http\Requests\AdminProfileRequest;
use App\Models\Admin;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Hash;

class AdminProfileController extends Controller
{
    public function profile()
    {
        $user = Admin::where('id', auth()->user()->id)->first();
        return view('Admin.pages.profile.updateProfile', compact('user'));
    }

    public function updateProfile(AdminProfileRequest $profileRequest)
    {
        $user = auth()->user();
        $data = $profileRequest->validated();

        // ✳️ منطق الصور كما هو (ما لمسناهوش)
        if (isset($data['image'])) {
            if ($user->image) {
                (new ImageManger())->deleteImage($user->image);
            }
            $imagePath = (new ImageManger())->uploadImage('uploads/users', $data['image']);
            $data['image'] = $imagePath;
        }

        // ✳️ إضافة بسيطة للباسورد: اختياري - لو اتبعت يتعمله hash ويتحدّث
        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']); // علشان ما يبوظش التحديث لو فاضي
        }

        $user->update($data);

        Session::flash('message', ['type' => 'success', 'text' => __('User updated successfully')]);
        return redirect()->back();
    }
}
