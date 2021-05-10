<?php
namespace Security;
use Helper\Session;
/**
 * THIS IS SIMPLY AN EXAMPLE SUMMARY.
 * A summary informing the user what the associated element does.
 *
 * A *description*, that can span multiple lines, to go _in-depth_ into
 * the details of this element and to provide some background information
 * or textual references.
 *
 * @param string $myArgument With a *description* of this argument,
 *                           these may also span multiple lines.
 *
 * @return void
 */
class Hash
{
    private $specifiedHash;

    public function __construct($hashType = PASSWORD_DEFAULT)
    {
        $this->specifiedHash = $hashType;
    }

    public function hash($data)
    {
        return password_hash($data, $this->specifiedHash);
    }

    public function verify($exposedData, $hashedData)
    {
        return password_verify($exposedData, $hashedData);
    }

    public function IsOldHash($hashedValue)
    {
        return password_needs_rehash($hashedValue, $this->specifiedHash);
    }

    public static function generateCSRFToken() 
    {
        Session::set("token", bin2hex(random_bytes(32)));
        return Session::get("token");
    }

    public static function validCSRFToken()
    {
        if (!empty($_POST['token']))
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
}
?>