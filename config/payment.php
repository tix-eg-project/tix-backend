<?php
// config/payment.php (append your own keys alongside Paymob)
return [
    // Paymob keys...
    //'paymob_url' => env('PAYMOB_URL', 'https://accept.paymob.com/api'),
    // ...

    // XPay
    'xpay_base' => env('XPAY_BASE_URL', 'https://staging.xpay.app/api/v1'),
    'xpay_api_key' => env('XPAY_API_KEY'),
    'xpay_community_id' => env('XPAY_COMMUNITY_ID'),
    'xpay_variable_amount_id' => env('XPAY_VARIABLE_AMOUNT_ID'),
    'xpay_add_fees' => env('XPAY_ADD_FEES', false),
];
