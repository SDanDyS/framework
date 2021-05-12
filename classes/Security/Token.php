<?php
    namespace Security;
    use Helper\Session;
    class Token
    {
        public static function generateCSRFToken() 
        {
            Session::set("token", bin2hex(random_bytes(32)));
            return Session::get("token");
        }
    
        public static function validCSRFToken()
        {
            if (hash_equals(Session::get("token"), $_POST['token'])) 
            {
                    return true;
            } else 
            {
                return false;
                    // Log this as a warning and keep an eye on these attempts RETURN FALSE AFTER LOGGING
            }
        }
    }
?>