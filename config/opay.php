<?php

// return [
//     'merchant_id'  => env('OPAY_MERCHANT_ID'),
//     'public_key'   => env('OPAY_PUBLIC_KEY'),
//     'secret_key'   => env('OPAY_SECRET_KEY'),

//     'return_url'   => env('OPAY_RETURN_URL'),
//     'cancel_url'   => env('OPAY_CANCEL_URL'),
//     'callback_url' => env('OPAY_CALLBACK_URL'),

//     'endpoints' => [
//         'cashier_create' => env('OPAY_ENV', 'sandbox') === 'production'
//             ? 'https://api.opaycheckout.com/api/v1/international/cashier/create'
//             : 'https://sandboxapi.opaycheckout.com/api/v1/international/cashier/create',

//         'cashier_status' => env('OPAY_ENV', 'sandbox') === 'production'
//             ? 'https://api.opaycheckout.com/api/v1/international/cashier/status'
//             : 'https://sandboxapi.opaycheckout.com/api/v1/international/cashier/status',
//     ],
// ];



return [
    'merchant_id'  => env('OPAY_MERCHANT_ID'),
    'public_key'   => env('OPAY_PUBLIC_KEY'),
    'secret_key'   => env('OPAY_SECRET_KEY'),

    'return_url'   => env('OPAY_RETURN_URL'),
    'cancel_url'   => env('OPAY_CANCEL_URL'),
    'callback_url' => env('OPAY_CALLBACK_URL'),

    'endpoints' => [
        'cashier_create' => env('OPAY_ENV', 'sandbox') === 'production'
            ? 'https://api.opaycheckout.com/api/v1/international/cashier/create'
            : 'https://testapi.opaycheckout.com/api/v1/international/cashier/create',

        'cashier_status' => env('OPAY_ENV', 'sandbox') === 'production'
            ? 'https://api.opaycheckout.com/api/v1/international/cashier/status'
            : 'https://testapi.opaycheckout.com/api/v1/international/cashier/status',
    ],
];
