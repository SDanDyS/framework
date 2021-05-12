<?php
    namespace Security;
    class Url
    {
        public static function getRequestMethod()
        {
            return $_SERVER["REQUEST_METHOD"];
        }

        public static function isHttps(bool $securityThreat)
        {
            if (!$securityThreat)
            {
                return true;
            }

            return isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off';
        }
    }
?>