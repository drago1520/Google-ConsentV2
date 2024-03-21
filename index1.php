<?php 

#-> JSON DATA
$jsonData = array(
    'userName' => "999063",
    'password' => "2482962322",
    'language' => "BG",
 );

 ############
 #-> Create Shipment Request
$jsonData = json_encode($jsonData);

// The URL you're sending the POST request to
$url = 'https://api.speedy.bg/v1/client/contract/';

// Initialize cURL session
// Initialize cURL session
$ch = curl_init($url);

// Set cURL options
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Content-Length: ' . strlen($jsonData)
]);

// Execute cURL session and capture the response
$response = curl_exec($ch);

// Check for cURL errors
if (curl_errno($ch)) {
    throw new Exception(curl_error($ch));
}

// Close cURL session
curl_close($ch);

// Decode the response to an associative array
$responseArray = json_decode($response, true);

// Check if json_decode was successful
if (json_last_error() === JSON_ERROR_NONE) {
    // Print the response in human-readable JSON format and formatted beautifully
    echo json_encode($responseArray, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
} else {
    echo "Error decoding JSON response.";
}