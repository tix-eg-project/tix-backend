<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\Shipping\VSoftShippingService;

class VSoftSyncProducts extends Command
{
    protected $signature = 'vsoft:sync-products';
    protected $description = 'List available VSoft products';

    public function handle(VSoftShippingService $api)
    {
        $products = $api->getProducts();
        foreach ($products as $p) {
            $this->line(($p['productID'] ?? '?') . ' — ' . ($p['productName'] ?? ''));
        }
        return self::SUCCESS;
    }
}
