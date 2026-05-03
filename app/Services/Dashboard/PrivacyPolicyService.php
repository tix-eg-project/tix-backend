<?php

namespace App\Services\Dashboard;

use App\Models\PrivacyPolicy;
use Illuminate\Support\Facades\DB;

class PrivacyPolicyService
{
    public function get()
    {
        return PrivacyPolicy::first();
    }

    public function update(PrivacyPolicy $policy, array $data)
    {
        return DB::transaction(function () use ($policy, $data) {
            $policy->update([
                'content_ar' => $data['content_ar'],
                'content_en' => $data['content_en'],
            ]);
            return $policy;
        });
    }

    public function createIfNotExist()
    {return PrivacyPolicy::firstOrCreate([], [
            'content_ar' => '',
            'content_en' => ''
        ]);
        return PrivacyPolicy::firstOrCreate([]);
    }
}
