<?php
require __DIR__ . '/../../vendor/autoload.php'; // Autoload Twilio SDK

use Twilio\Rest\Client;

// Dine Twilio-legitimasjoner
$sid = 'REPLACE_WITH_TWILIO_SID'; // Erstatt med ditt Twilio Account SID
$token = 'REPLACE_WITH_TWILIO_AUTH_TOKEN'; // Erstatt med ditt Twilio Auth Token
$twilio_number = 'REPLACE_WITH_TWILIO_NUMBER'; // Erstatt med ditt Twilio-nummer

$client = new Client($sid, $token);

// Telefonnummer og melding
$mottaker = $mottakerTlf; // Mottakerens telefonnummer (bruk internasjonalt format)
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