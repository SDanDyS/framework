<?php
namespace User;
use DataHandler\Recordset;
use Encryption\Encryption;
class Login
{
    private $table;
    private $databaseAccess;
    private $hash;
    private $userCredentials = [];
    private $userPwd;
    
    public function __construct($table)
    {
        if (self::requestMethod() === "GET")
        {
            exit('Forbidden to use $_GET as login request method!');
        }
        $this->setHash();
        $this->table = $table;
        $this->databaseAccess = new Recordset($table);
    }

    public static function requestMethod()
    {
        return $_SERVER["REQUEST_METHOD"];
    }

    public function setCredentials(...$loginCredentials)
    {
        /*
        * Force a $_POST request for security reasons
        */
        $this->userCredentials = $loginCredentials;
    }

    public function setPassword($pwd)
    {
        $this->userPwd = $pwd;
    }

    public function setHash($hash = PASSWORD_DEFAULT)
    {
        $this->hash = new Encryption($hash);
    }

    public function login()
    {
        $completedInfo = [];

        $query = "SELECT * FROM `{$this->table}` WHERE";

        foreach($this->userCredentials as $count => $individualKey)
        {
            if (count($this->userCredentials) - 1 === $count)
            {
                $query = $query . " {$individualKey} = ?";
            } else
            {
                $query = $query . " {$individualKey} = ? AND";
            }
            $completedInfo[] = $_POST[$individualKey];
        }
        $this->databaseAccess->prepare($query, ...$completedInfo);

        if($this->uniqueUser())
        {
            if($this->verifyPassword())
            {
                /*
                * check whether user needs a rehash
                *If there is an update, the password hash will be updated, if not, the hash will be changed
                * Whether user needs an update or not, the hash will be changed for security reasons
                */
                $keeper = $_POST["{$this->userPwd}"];

                unset($_POST["{$this->userPwd}"]);

                $this->databaseAccess->setField("{$this->userPwd}", $this->hash->updateHash($this->databaseAccess->getField($this->userPwd)));
                $this->databaseAccess->save();

                $_POST["{$this->userPwd}"] = $keeper;

                $userConstructed = $this->constructUser();

                return $userConstructed;
            } else
            {
                return false;
            }
        } else 
        {
            return false;
        }
    }

    private function uniqueUser()
    {
        $userCount = $this->databaseAccess->getRow();

        if (count($userCount) > 1)
        {
            return false;
        }

        return true;
    }

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

    public static function sessionStart()
    {
        self::sessionEnabled();

        if (session_status() === PHP_SESSION_NONE)
        {
            session_start();
        }
    }

    private function verifyPassword()
    {
        if ($this->hash->verify($_POST[$this->userPwd], $this->databaseAccess->getField($this->userPwd)))
        {
            return true;
        }
        
        return false;
    }

    private function constructUser()
    {
        self::sessionStart();

        $recordset = $this->databaseAccess->getRow(0);

        foreach ($recordset as $key => $value)
        {
            //DO NOT STORE PASSWORD FOR SECURITY REASONS
            if ($key === $this->userPwd)
            {
                continue;
            }

            $_SESSION["user"][$key] = $value;
        }
        return true;
    }
}
?>