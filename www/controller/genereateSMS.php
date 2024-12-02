<?php
require __DIR__ . '/../../vendor/autoload.php'; // Autoload Twilio SDK

use Twilio\Rest\Client;

// Your Twilio informaation
$sid = //'AC2d5d6a5ae7ead2afbf566af96efdea8c'; // Replace with your SID
$token = //'aa3708ac725b400ec8e25152e1521654'; // Replace with your Auth Token
$twilio_number = //'+15672298275'; // Replace with your Twilio-nummer

$client = new Client($sid, $token);

// Phone number and message
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