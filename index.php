<?php

header("Content-Type: application/json");

// Read JSON body
$input = file_get_contents("php://input");

if (!$input) {
    echo json_encode(["error" => "No JSON received"]);
    exit;
}

// Read ALL incoming headers (Render-friendly)
$incoming = getallheaders();

$timestamp  = $incoming["BinancePay-Timestamp"]      ?? "";
$nonce      = $incoming["BinancePay-Nonce"]          ?? "";
$cert       = $incoming["BinancePay-Certificate-SN"] ?? "";
$signature  = $incoming["BinancePay-Signature"]      ?? "";

// Forward headers to Binance
$headers = [
    "Content-Type: application/json",
    "BinancePay-Timestamp: $timestamp",
    "BinancePay-Nonce: $nonce",
    "BinancePay-Certificate-SN: $cert",
    "BinancePay-Signature: $signature"
];

// DEBUG (optional)
// file_put_contents("debug.txt", print_r($incoming, true));

$ch = curl_init("https://bpay.binanceapi.com/binancepay/openapi/v2/order");
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $input);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$response = curl_exec($ch);
$error = curl_error($ch);
curl_close($ch);

if ($error) {
    echo json_encode(["curl_error" => $error]);
    exit;
}

echo $response;
