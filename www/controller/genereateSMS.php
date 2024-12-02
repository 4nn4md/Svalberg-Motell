<?php
require __DIR__ . '/../../vendor/autoload.php'; // Autoload Twilio SDK

use Twilio\Rest\Client;

// Your Twilio informaation
$sid = 'REPLACE_WITH_TWILIO_SID'; // Replace with your SID
$token = 'REPLACE_WITH_TWILIO_AUTH_TOKEN'; // Replace with your Auth Token
$twilio_number = 'REPLACE_WITH_TWILIO_NUMBER'; // Replace with your Twilio-nummer

$client = new Client($sid, $token);

// Phone numer and message
$mottaker = $mottakerTlf; 
$melding = "
Takk for din bestilling! 
Vi bekrefter at alt er registrert. 
Du ankommer den $checkin og reiser den $checkout. 
Vi gleder oss til å ønske deg velkommen!";

try {
    $message = $client->messages->create(
        $mottaker,
        [
            'from' => $twilio_number,
            'body' => $melding
        ]
    );
} catch (Exception $e) {
    echo "Feil under sending: " . $e->getMessage() . PHP_EOL;
}
?>