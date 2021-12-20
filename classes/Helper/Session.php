<?php
namespace Helper;
class Session
{

    public static function set(string $key, mixed $value) : void
    {
        $_SESSION[$key] = $value;
    }

    public static function get(string $key) : mixed
    {
        return $_SESSION[$key];
    }

    public static function unset(string $key = null)
    {
        if (is_null($key))
        {
            unset($_SESSION);
        } else
        {
            unset($_SESSION[$key]);
        }
    }
    
    public static function enabled() : void
    {
        if (session_status() === PHP_SESSION_DISABLED)
        {
            exit("Sessions are disabled.");
        }  
    }

    public static function destroy() : void
    {
        self::enabled();

        if (session_status() === PHP_SESSION_ACTIVE)
        {
            session_unset();
            session_destroy();
        }
    }

    public static function start() : bool
    {
        self::enabled();

        if (session_status() === PHP_SESSION_NONE)
        {
            return session_start();
        }
        
        return false;
    }
}
?>