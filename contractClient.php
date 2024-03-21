<?php 

#-> JSON DATA
$jsonData = array(
    'userName' => SPEEDY_API_USERNAME,
    'password' => SPEEDY_API_PASSWORD,
    'language' => LANGUAGE
 );
 
 #-> Get Contract Clients Request
 $jsonResponse = apiRequest(SPEEDY_API_BASE_URL.'client/contract/', $jsonData);
 $jsonResponse = json_decode($jsonResponse, true);
 
 #-> Print the result
 print_r($jsonResponse);