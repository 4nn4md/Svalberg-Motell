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

?>
