<?php

namespace App\Services\Shipping;

use Illuminate\Support\Facades\Http;

class VSoftShippingService
{
    private string $base;
    private array $headers;

    public function __construct()
    {
        // [CHANGED] نقرأ من .env مباشرةً زي ما عندك
        $this->base = rtrim(env('VSOFT_BASE_URL', 'https://vsoftapi.com-eg.net/api/ClientUsers/V6'), '/');

        $this->headers = [
            'AccessToken'  => (string) env('VSOFT_ACCESS_TOKEN'),
            'CompanyID'    => (string) env('VSOFT_COMPANY_ID'),
            'Accept'       => 'application/json',
            'Content-Type' => 'application/json',
        ];
    }

    public function getCities(): array
    {
        $res = Http::withHeaders($this->headers)
            ->timeout(60)->connectTimeout(15)->retry(3, 2000) // [ADDED]
            ->get("{$this->base}/GetCities");

        $res->throw();
        return $res->json() ?? [];
    }

    public function getProducts(): array
    {
        $res = Http::withHeaders($this->headers)
            ->timeout(60)->connectTimeout(15)->retry(3, 2000) // [ADDED]
            ->get("{$this->base}/GetProducts");

        $res->throw();
        return $res->json() ?? [];
    }

    public function saveShipments(array $payload): array
    {
        $res = Http::withHeaders($this->headers)
            ->timeout(60)->connectTimeout(15)->retry(3, 2000) // [ADDED]
            ->post("{$this->base}/SaveShipmentEx", $payload);

        $res->throw();
        return $res->json() ?? [];
    }
}
