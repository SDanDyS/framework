<?php
    namespace Security;
    use Helper\Session;
    class Generic
    {
        //ONLY INSERT FUNCTIONS / CLASSES IN THIS FILE WHICH WILL BE USED BY DEV
        //DO NOT CREATE YOUR PHP SCRIPT IN THIS FILE

        public static function escapeHtmlEntities($data)
        {
          $data = trim($data);
          $data = stripslashes($data);
          $data = htmlspecialchars($data);
          return $data;
        }
    
        public static function validate_phone_number($phone)
        {
            // Allow +, - and . in phone number
            $filtered_phone_number = filter_var($phone, FILTER_SANITIZE_NUMBER_INT);
            // Remove "-" from number
            $phone_to_check = str_replace("-", "", $filtered_phone_number);
            // Check the lenght of number
            // This can be customized if you want phone number from a specific country
            if (strlen($phone_to_check) < 9 || strlen($phone_to_check) > 14) 
            {
                return false;
            } else 
            {
                return true;
            }
        }
    
        public static function validateEmail($email) 
        {
            return filter_var($email, FILTER_VALIDATE_EMAIL);
        }
    
        public static function validPaymentValue($value)
        {
            if (preg_match('/^[0-9]+(\.[0-9]{1,2})?$/', $value))
            {
                return true;
            }
            return false;
        }
    }