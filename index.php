<?php

header("Content-Type: application/json");

// Read the incoming POST JSON
$input = file_get_contents("php://input");

if (!$input) {
    echo json_encode(["error" => "No JSON received"]);
    exit;
}

// Binance API endpoint
$binanceUrl = "https://bpay.binanceapi.com/binancepay/openapi/v2/order";

// Forward required headers from WHMCS to Binance
$headers = [
    "Content-Type: application/json",
    "BinancePay-Timestamp: " . ($_SERVER["HTTP_BINANCEPAY_TIMESTAMP"] ?? ""),
    "BinancePay-Nonce: " . ($_SERVER["HTTP_BINANCEPAY_NONCE"] ?? ""),
    "BinancePay-Certificate-SN: " . ($_SERVER["HTTP_BINANCEPAY_CERTIFICATE_SN"] ?? ""),
    "BinancePay-Signature: " . ($_SERVER["HTTP_BINANCEPAY_SIGNATURE"] ?? "")
];

// Initialize curl
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $binanceUrl);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $input);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$response = curl_exec($ch);
$error = curl_error($ch);
curl_close($ch);

// Error handling
if ($error) {
    echo json_encode(["curl_error" => $error]);
    exit;
}

echo $response;
