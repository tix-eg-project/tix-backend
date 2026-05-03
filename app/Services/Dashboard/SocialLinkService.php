<?php

namespace App\Services\Dashboard;

use App\Models\SocialLink;

class SocialLinkService
{
    public function index()
    {
        return SocialLink::latest()->get();
    }

    public function store(array $data)
    {
        return SocialLink::create($data);
    }

    public function update(SocialLink $link, array $data)
    {
        $link->update($data);
        return $link;
    }

    public function delete(SocialLink $link)
    {
        return $link->delete();
    }
}
