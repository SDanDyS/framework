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

    protected function readParameters($keys)
    {
        $param = [];

        for ($i = 0; $i < count($keys); $i++)
        {
            if (array_key_exists($keys[$i], $this->credentials))
            {
                $param[$keys[$i]] = $this->credentials[$keys[$i]];
            } else
            {
                exit("The key <b>{$keys[$i]}</b> is not set!");
            }
        }
        return $param;
    }

    public function dataExists(...$keys)
    {
        $whereClause = "";
        $i = 0;

        $param = $this->readParameters($keys);

        foreach ($param as $key => $value)
        {
            $i++;

            if (count($param) == 1)
            {
                $whereClause .= "{$key} = ?";
            } else
            {
                if (count($param) == $i)
                {
                    $whereClause .= "{$key} = ?";
                } else
                {
                    $whereClause .= "{$key} = ? AND ";
                }
            }
        }

        $this->database->prepare("SELECT * FROM `{$this->table}` WHERE {$whereClause}", ...array_values($param));

        if (!empty($this->database->getField($this->database->getPrimaryKey())))
        {
            return true;
        }
        
        return false;
    }
}
?>