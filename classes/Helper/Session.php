<?php
namespace Helper;
class Session
{

    public static function set(string $key, mixed $value)
    {
        $_SESSION[$key] = $value;
    }

    public static function get(string $key)
    {
        return $_SESSION[$key];
    }

    public static function unset(string|null $key = null)
    {
        if (is_null($key))
        {
            unset($_SESSION);
        } else
        {
            unset($_SESSION[$key]);
        }
    }
    
    public static function enabled()
    {
        if (session_status() === PHP_SESSION_DISABLED)
        {
            exit("Sessions are disabled.");
        }  
    }

    public static function destroy()
    {
        self::enabled();

        if (session_status() === PHP_SESSION_ACTIVE)
        {
            session_unset();
            session_destroy();
        }
    }

    public static function start()
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