<?php
require_once($_SERVER['DOCUMENT_ROOT'] . "/Svalberg-Motell/www/assets/inc/db.php");
/*
This function calculate the price for each room when it comes to number of
people staying in that room. it also consider the base price for that room. 
 */
function price_guests($base_price, $adults, $children) {
    //Price for each afult/children
    $price_adult = 400;
    $price_children = 150;

    //calculate the total price for the guest depending on how many people.
    $guest_price = ($adults * $price_adult) + ($children * $price_children);

    //claculate the total price using the $guest_price and $base_price for that room. 
    $total_price = $base_price + $guest_price;

    return $total_price;
}

//function GetRoomImage uses switch to look for the value of $type_name 
function GetRoomImage($type_name){
 
    switch ($type_name) {
        // If $type_name id 'Standardrom', it will return imagefile 'standardrom.avif'
        case 'Standardrom':
            return 'standardrom.avif';
        case 'Dobbeltrom':
            return 'dobbelrom.jpg';
        case 'Superior Rom':
            return 'superior_rom.jpg';
        case 'Familie Suite':
            return 'familie_rom.jpg';
        case 'Deluxerom':
            return 'deluxrom.jpg';
        case 'Honeymoon Suite':
            return 'honeymoon.jpg';
    }
}

function calculateMVA($base_price){
    // Convert the base price to a float to ensure it's a valid number
    //$base_price = (int) $base_price;
    $base_priceMVA = ($base_price * 12) / 100;
    return $base_priceMVA; 
}

function log_error($e) {
    $error_message = "Error time: " . date('d-m-y H:i:s') . " - Error message: " . $e->getMessage() . "\n";
    // Path to the log file
    $log_file = $_SERVER['DOCUMENT_ROOT'] . '/Svalberg-Motell/private/log/log.txt';

    // Check if the directory is writable, if not, change permissions
    if (!is_writable($log_file)) {
        chmod($log_file, 0666);  
    }
    // Write the error message to the log file
    file_put_contents($log_file, $error_message, FILE_APPEND);
}

function sanitize($var) {
    $var = strip_tags($var); // Fjern HTML-tagger
    $var = htmlentities($var); // Konverter spesialtegn til HTML-enheter
    $var = trim($var);
    $var = stripslashes($var);
    $var = htmlspecialchars($var);
    return basename($var); // Sørg for at bare filnavnet returneres, forhindrer directory traversal
}

function calculatePriceWithPoints($price, $points){
    $pointValue = 0.20;
    $maxDiscount = $points * $pointValue;
    if ($maxDiscount >= $price) {
        // Hele prisen dekkes av poengene
        $pointsUsed = ceil($price / $pointValue); // Beregn hvor mange poeng som faktisk trengs
        $pointsLeft = $points - $pointsUsed; // Poeng som blir igjen
        return [
            'price' => 0.0, // Hele prisen dekkes
            'pointsLeft' => $pointsLeft
        ];
    }
    $newPrice = $price - $maxDiscount; // Ny pris etter rabatt
    return [
        'price' => $newPrice,
        'pointsLeft' => 0 // Ingen poeng igjen
    ];
}

function redirectAction($action, $redirectUrl) {
    if (isset($_GET['action']) && $_GET['action'] === $action) {
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['redirect_step'] = 'booking_3';
            error_log("Redirect step set to: " . $_SESSION['redirect_step']);
            header("Location: /Svalberg-Motell/www/$redirectUrl");
            exit();
        }
    }
}

?>
