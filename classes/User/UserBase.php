<?php
namespace User;
use Helper\Session;
use Security\UrlAccessibility;
use DataHandler\Recordset;

class UserBase
{
    protected $table;
    protected $database;
    protected $credentials = [];
    
    public function __construct($table, $forceHttps = true)
    {
        if (UrlAccessibility::getRequestMethod() === "GET")
        {
            exit('Forbidden to use $_GET as login request method!');
        } else if (!UrlAccessibility::isHttps($forceHttps))
        {
            exit('Cannot login without an HTTPS request!');
        } else
        {
            $this->table = $table;

            $this->database = new Recordset($table);
        }
    }

    public function setField($key, $value)
    {
        $this->database->setField($key, $value, true);
        
        $this->credentials[$key] = $value;
    }

    public function getField($key)
    {
        return $this->database->getField($key);
    }

    public function dataExists()
    {
        $whereClause = "";
        $i = 0;

        foreach ($this->credentials as $key => $value)
        {
            $i++;

            if (count($this->credentials) == 1)
            {
                $whereClause .= "{$key} = ?";
            } else
            {
                if (count($this->credentials) == $i)
                {
                    $whereClause .= "{$key} = ?";
                } else
                {
                    $whereClause .= "{$key} = ? AND ";
                }
            }
        }
        
        $this->database->prepare("SELECT * FROM `{$this->table}` WHERE {$whereClause}", ...array_values($this->credentials));

        if (!empty($this->database->getField($this->database->getPrimaryKey())))
        {
            return true;
        }
        
        return false;
    }
}
?>