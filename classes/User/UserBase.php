<?php
namespace User;
use Helper\Session;
use Security\UrlAccessibility;
use DataHandler\Recordset;

class UserBase
{
    protected $table;
    protected $database;
    protected $userCredentials = [];
    protected $userPwd;
    
    public function __construct($table)
    {
        if (UrlAccessibility::getRequestMethod() === "GET")
        {
            exit('Forbidden to use $_GET as login request method!');
        } else if (!UrlAccessibility::isHttps())
        {
            exit('Cannot send request without an SSL key!');
        } else
        {
            $this->table = $table;

            $this->database = new Recordset($table);
        }
    }

    public function setField($key, $value)
    {
        $this->database->setField($key, $value, true);
    }

    public function getField($key)
    {
        return $this->getField($key);
    }
}
?>