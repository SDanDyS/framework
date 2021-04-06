<?php
namespace Helper;
class Session
{
    public static function sessionEnabled()
    {
        if (session_status() === PHP_SESSION_DISABLED)
        {
            exit("Sessions are disabled. Enable sessions.");
        }  
    }

    public static function sessionDestroy()
    {
        self::sessionEnabled();

        if (session_status() === PHP_SESSION_ACTIVE)
        {
            session_unset();
            session_destroy();
        }
    }

    public static function init()
    {
        self::sessionEnabled();

        if (session_status() === PHP_SESSION_NONE)
        {
            return session_start();
        }
        return false;
    }
}
?>