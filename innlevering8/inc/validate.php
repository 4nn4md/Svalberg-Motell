<?php
class Validering{
    // All error message will be placed in this array
    private $validateError = [];

    // Validate if specific fields (fname, lname, tlf, email) are empty
    function validereFeltene($email, $password, $role){
        if (empty($email) || empty($password) || empty($role)) {
            $this->validateError[] = "Fyll in alle nødvendige informasjon.";
            return true;
        }
        return false;
    }

    function validereEpost($epost){
        if(!filter_var($epost, FILTER_VALIDATE_EMAIL)){
            $this->validateError[] = "Epost er ikke gyldig.";
        }
    }

    function validateEpostStaff($email){
        if(!empty($email)){
            if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
                $this->validateError[] = "Epost er ikke gyldig.";
            }
        }

        if (!str_ends_with($email, '@svalberg.no')) { 
            $this->validateError[] = "Epost må slutte på @svalberg.no.";
            return false;
        }
        return true;
    }

    // Return the array list of errors
    function getValidateError() {
        return $this->validateError;
    }
  
}
?>
