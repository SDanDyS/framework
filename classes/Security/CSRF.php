<?php
    namespace Security;
    use Helper\Session;
    use System\FileSystem;

    class CSRF
    {
        public static function generateToken() : string
        {
            Session::set("token", bin2hex(random_bytes(32)));
            return Session::get("token");
        }
    
        public static function validateToken() : bool
        {
            if (hash_equals(Session::get("token"), $_POST['token'])) 
            {
                    Session::unset("token");
                    return true;
            } else 
            {
                return false;
                    // Log this as a warning and keep an eye on these attempts RETURN FALSE AFTER LOGGING
            }
        }
    }
?>