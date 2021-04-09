<?php
namespace Security;

class UrlAccessibility
{
    public static function getRequestMethod()
    {
        return $_SERVER["REQUEST_METHOD"];
    }

    public static function isHttps($securityThreat)
    {
        if (!$securityThreat)
        {
            return true;
        }

        return isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off';
    }
}
?>