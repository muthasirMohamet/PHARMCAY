<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form inputs
    $senderName = $_POST['senderName'];
    $receiverNumber = $_POST['receiverNumber'];
    $messageBody = $_POST['messageBody'];

    // Hormuud API configuration
    $authUrl = 'https://smsapi.hormuud.com/token';
    $smsUrl = 'https://smsapi.hormuud.com/api/SendSMS';

    // Authentication data
    $authData = [
        'Username' => 'muthasir', // Replace with your username
        'Password' => 'Muthasir92900@', // Replace with your password
        'grant_type' => 'password'
    ];

    // Step 1: Get access token
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_POST, 1);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_URL, $authUrl);
    curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($authData));

    $response = curl_exec($curl);
    curl_close($curl);

    $character = json_decode($response);
    if (!isset($character->access_token)) {
        die("Error: Unable to fetch access token.");
    }
    $accessToken = $character->access_token;

    // Step 2: Send SMS
    $smsData = [
        "mobile" => $receiverNumber,
        "message" => $messageBody,
        "senderid" => $senderName
    ];

    $headers = [
        "Content-Type: application/json; charset=utf-8",
        "Authorization: Bearer $accessToken"
    ];

    $ch = curl_init($smsUrl);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($smsData));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    $result = curl_exec($ch);
    curl_close($ch);

    echo "Response from API: $result";
}
?>
