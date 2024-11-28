<?php

class Validering{
    // All error message will be placed in this array
    private $validateError = [];

    function validereFornavn($fname){
        // Checks if the name contains numbers
        if (!is_string($fname)){
            $this->validateError[] = "Kan ikke inneholde tall";
        }

        // Checks if the name contains special characters 
        if (preg_match('/[^a-zA-ZÆØÅæøå\s]/', $fname)) {
            $this->validateError[] = "Fornavnet kan ikke inneholde spesielle tegn";
        }
    }

    function validereEtternavn($lname){

        // Checks if the name contains numbers
        if (!is_string($lname)){
            $this->validateError[] = "Kan ikke inneholde tall";
        }

        // Checks if the name contains special characters 
        if (preg_match('/[^a-zA-ZÆØÅæøå\s]/', $lname)) {
            $this->validateError[] = "Fornavnet kan ikke inneholde spesielle tegn";
        }
    }

    // Validate email
    function validereEpost($epost){
        if(!filter_var($epost, FILTER_VALIDATE_EMAIL)){
            $this->validateError[] = "Epost " . $epost . " er ikke gyldig." . "<br>";
        }
    }

    function validereMobilnummer($country_code, $mobile){

        if (preg_match('/^\+/', $country_code)) {
            // Fjern pluss-tegnet hvis det finnes
            $country_code = substr($country_code, 1);
        }

        // Validate if country code is less than 4 or more than 1
        if (strlen($country_code) < 1 || strlen($country_code) > 4) {
            $this->validateError[] = "Landskoden er ugyldig (må være 1-4 sifre)";
        }

        // Validate if county code is not numeric
        if (!is_numeric($country_code)){
            $this->validateError[] = "Landskoden er ugyldig (kan bare inneholde sifre)";
        }

        // Validate if the number is not equal 8
        if (strlen($mobile) != 8) {
            $this->validateError[] = "Mobilnummeret er ugyldig (må være 8 sifre)";
        }
        // Validate if the number is not numeric
        if(!is_numeric($mobile)) {
            $this->validateError[] = "Mobilnummeret er ugyldig, kan bare inneholde tall";
        }
    }

    function validereMessage($message){
        
        // Can not be more than 160 characters 
        if (strlen($message) > 160) {
            $this->validateError[] = "Meldingen kan ikke være lengre enn 160 tegn";
        }
    }

    function validereFeltene(){
        if (empty($fname) || empty($lname) || empty($email) || empty($choosePayment)) {
            $this->validateError[] = "Fyll in alle nødvendige informasjon.";
        }
    }
    
    function getValidateError() {
        return $this->validateError;
    }

 
    
    
    
}

?>