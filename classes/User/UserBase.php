<?php
namespace User;
use Helper\Session;
use \Security;
use DataHandler\Recordset;

abstract class UserBase
{
    protected $table;
    protected $database;
    protected $credentials = [];
    protected static $hash;
    
    public function __construct($table, $forceHttps = true)
    {
        if (Security\UrlAccessibility::getRequestMethod() === "GET")
        {
            exit('Forbidden to use $_GET as request method!');
        } else if (!Security\UrlAccessibility::isHttps($forceHttps))
        {
            exit('Cannot proceed without an HTTPS request!');
        } else
        {
            $this->table = $table;

            $this->database = new Recordset($table);

            if (empty(self::$hash))
            {
                $this->setHash();
            }
        }
    }

    public function setHash($hashMethod = PASSWORD_DEFAULT)
    {
        self::$hash = new Security\Hash($hashMethod);
    }

    public function setField($key, $value, $encrypt = false)
    {
        if ($encrypt)
        {
            $this->database->setField($key, self::$hash->hash($value), true);
            $this->credentials[$key] = $value;
        } else
        {
            $this->database->setField($key, $value, true);
            $this->credentials[$key] = $value;           
        }
    }

    public function getField($key, $decrypt = false)
    {
        if (!$decrypt)
        {
            return $this->database->getField($key);
        } else
        {
            return self::$hash->verify($this->credentials[$key], $this->database->getField($key));
        }
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

        //SET INDEX TO 0 AND THEN SHIFT TO 1
        //THE FIRST ENTRY (1) WILL RETURN WHETHER USER ALREADY EXISTS OR NOT
        $this->database->setIndex();
        $this->database->next();

        $this->database->prepare("SELECT * FROM `{$this->table}` WHERE {$whereClause}", ...array_values($param));

        //PREPARE WILL CAUSE THE INDEX TO RESET AFTER ITS FINISHED
        //FORCE THE INDEX BACK TO 1
        $this->database->setIndex();
        $this->database->next();

        if (!empty($this->database->getField($this->database->getPrimaryKey())))
        {
            $this->database->clearCache(1);
            $this->database->resetIndex();
            return true;
        }

        $this->database->clearCache(1);
        $this->database->resetIndex();
        return false;
    }
}
?>