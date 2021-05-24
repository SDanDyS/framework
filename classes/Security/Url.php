<?php
    namespace Security;
    class Url
    {
        public static function getRequestMethod() : string
        {
            return $_SERVER["REQUEST_METHOD"];
        }

        public static function isHttps(bool $securityThreat) : bool
        {
            if (!$securityThreat)
            {
                return true;
            }

            return isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off';
        }
    }
?>