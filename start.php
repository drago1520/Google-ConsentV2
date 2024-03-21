<?php 
#-> I. SENDER
$senderArray = array(
   'phone1' => array('number' => '0888112233'),
   'contactName' => 'Test API AJAX price',
   /* 'clientId' => 1234567890 */ // Not required
);

#-> For drop off shipment (The 'dropoffOfficeId' property overrides the address details)
//$senderArray['dropoffOfficeId'] = 2; // The 'dropoffOfficeId' can be obtained from the result of a Find Office Request.



#-> II. RECIPIENT
$recipientArray = array(
   'phone1' => array('number' => '0899445566'),	
   'privatePerson' => true,
   'clientName' => 'VASIL GEORGIEV',
   'email' => 'vasil@georgiev.bg'
);

#-> If the shipment is to an address. There are several options to define an address. Check the examples below.
$recipientAddressArray = array(
   'countryId' => 100, // BULGARIA. The 'countryId' can be obtained from the result of a Find Country Request.
   'siteId' => 68134, // SOFIA. The 'siteId' can be obtained from the result of a Find Site Request.
   'complexId' => 29, // A complex named 'KRASNA POLYANA 3'. The 'complexId' can be obtained from the result of a Find Complex Request.
   'streetId' => 3109, // A street named 'USTA GENCHO'. The 'streetId' can be obtained from the result of a Find Street Request.
   'streetNo' => '1A',
   'blockNo' => '301',
   'entranceNo' => '2',
   'floorNo' => '3',
   'apartmentNo' => '4'
);
if($do_kadetk == 2){
   $recipientArray['pickupOfficeId'] = 2;
}else{
   $recipientArray['address'] = $recipientAddressArray;
}




#-> III. SERVICE DETAILS
$serviceArray = array(
   'serviceId' => 505, // The 'serviceId' can be obtained from the result of a Destination Services Request.
);

/* Cash on delivery - Not mandatory */
$cashOnDeliveryArray = array(
   'amount' => $COD,
   'processingType' => 'CASH' // (CASH, POSTAL_MONEY_TRANSFER)
);

/* Options before payment - Not mandatory */
$optionsBeforePaymentArray = array(
   'option' => 'OPEN', 
   'returnShipmentServiceId' => 505,
   'returnShipmentPayer' => 'RECIPIENT' // (SENDER, RECIPIENT, THIRD_PARTY). The sender of the returning shipment is the recipient of the primary shipment.
);
/* 
* 'returnShipmentServiceId' is the service for the returning shipment in case the recipient refuses to accept the primary shipment. 
* It can be the same serviceId as in the primary shipment or one obtained from the result of a Destination Services Request.
*/


/* Additional services */
$additionalServicesArray = array(
   'obpd' => $optionsBeforePaymentArray,
);

if($isCOD && $COD > 0){
   $additionalServicesArray['cod'] = $cashOnDeliveryArray;
}

/* Add additional services to the main service array */
$serviceArray['additionalServices'] = $additionalServicesArray;



#-> IV. CONTENT OF THE PARCEL
$contentArray = array(
    "parcelsCount" => 1,
   'contents' => 'Материали',
   'package' => 'Кашон'
);

if ($pratka_kg > 0 & $pratka_vs > 0 & $pratka_dl > 0 & $pratka_sh > 0) {

  $contentArray['parcels'] = [ 
      [   
          "seqNo" => 1,
          "size" => [
              "height" => $pratka_vs,
              "length" => $pratka_dl,
              "width" => $pratka_sh
          ],
          "weight" => $pratka_kg,
          
      ] //Може да се добавят няколко парцели в 1 поръчка. ПР.: 1 кашон с обици, 1 кашон с гирлянди.
    ];

}elseif($pratka_kg > 0){
   //Ще вземе от нас изчисленото, което е по-голямо между обемно и нормално тегло. (Ние казваме speedy на какво финално тегло да начислят цена.)
   $totalWeight = $pratka_kg;
   $contentArray['totalWeight'] = $totalWeight;

}else{
   error_log("No weight provided. Стоката няма тегло и размери.");
   //# Какво, ако няма тегло дори?
}


#-> V. PAYMENTS
$paymentArray = array(
   'courierServicePayer' => 'RECIPIENT', // (SENDER, RECIPIENT, THIRD_PARTY)
   'declaredValuePayer' => 'RECIPIENT' // Mandatory only if the shipment has a 'declaredValue'.
);
      


#-> VI. JSON DATA
$jsonData = array(
   'userName' => "999063",
   'password' => "2482962322",
   'language' => "BG",
   'sender' => $senderArray, // You can skip the sender data. In this case the sender will be the default one for the username with all the address and contact information.
   'recipient' => $recipientArray,
   'service' => $serviceArray,
   'content' => $contentArray,
   'payment' => $paymentArray,
);



#-> Create Shipment Request
$jsonData = json_encode($jsonData);

 // The URL you're sending the POST request to
$url = 'https://api.speedy.bg/v1/shipment';

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
$response = json_decode($response, true);