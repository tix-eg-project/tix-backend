<?php

return [

    // ... باقي الكونفيجات (mail, services أخرى)

    // 'paysky' => [
    //     'merchant_id'      => env('PAYSKY_MERCHANT_ID'),                         // لازم في .env
    //     'terminal_id'      => env('PAYSKY_TERMINAL_ID'),                         // لازم في .env
    //     'secret'           => env('PAYSKY_SECRET'),                              // لازم في .env
    //     'currency'         => env('PAYSKY_CURRENCY', 'EGP'),
    //     'mode'             => env('PAYSKY_MODE', 'sandbox'),
    //     'return_url'       => env('PAYSKY_RETURN_URL'),                          // لازم في .env
    //     'callback_url'     => env('PAYSKY_CALLBACK_URL'),                        // لازم في .env
    //     // حطّ افتراضي لو تحب، أو سيبه فاضي عشان يبان بدري لو ناقص
    //     'payform_endpoint' => env('PAYSKY_PAYFORM_ENDPOINT', 'https://grey.paysky.io/PayFormPlus/api/Payment/Pay'),
    // ],

    'vsoft' => [
        'base'         => env('VSOFT_BASE', 'https://vsoftapi.com-eg.net/api/ClientUsers/V6'),
        'access_token' => env('VSOFT_ACCESS_TOKEN'),
        'company_id'   => env('VSOFT_COMPANY_ID'),
    ],


];
