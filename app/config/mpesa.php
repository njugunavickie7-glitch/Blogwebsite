<?php
// app/config/mpesa.php
// Fill these in from your Safaricom Daraja app (developer.safaricom.co.ke).
// Keep this file OUT of version control once real credentials are in it.
//
// IMPORTANT: callback_url must be a PUBLIC https URL that Safaricom can reach.
// On localhost use a tunnel like ngrok:  ngrok http 80
// then set callback_url to e.g. https://<id>.ngrok-free.app/Ismano/public/api/store/checkout/mpesa_callback.php

return [
    // 'sandbox' while testing, 'production' when live (Go-Live approved).
    'env' => 'sandbox',

    'consumer_key'    => 'YOUR_CONSUMER_KEY',
    'consumer_secret' => 'YOUR_CONSUMER_SECRET',

    // Lipa Na M-Pesa Online (STK) credentials:
    'shortcode' => '174379',                 // sandbox Paybill test shortcode
    'passkey'   => 'YOUR_LNM_PASSKEY',

    // 'CustomerPayBillOnline' for Paybill, 'CustomerBuyGoodsOnline' for Till.
    'transaction_type' => 'CustomerPayBillOnline',

    'callback_url' => 'https://YOUR_PUBLIC_HOST/Ismano/public/api/store/checkout/mpesa_callback.php',
];