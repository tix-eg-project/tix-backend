<?php

namespace App\Services\Dashboard;

use App\Models\ReturnPolicy;
use Illuminate\Support\Facades\DB;

class ReturnPolicyService
{
    public function get()
    {
        return ReturnPolicy::first();
    }

    public function update(ReturnPolicy $policy, array $data)
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
    {return ReturnPolicy::firstOrCreate([], [
            'content_ar' => '',
            'content_en' => ''
        ]);
        return ReturnPolicy::firstOrCreate([]);
    }
}
