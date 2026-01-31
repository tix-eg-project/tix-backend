<?php

namespace App\Services\Dashboard;

use App\Models\TermsPolicies;
use Illuminate\Support\Facades\DB;

class TermsConditionsService
{
    public function get()
    {
        return TermsPolicies::first();
    }

    public function update(TermsPolicies $policy, array $data)
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
    {
        return TermsPolicies::firstOrCreate([]);
    }
}
