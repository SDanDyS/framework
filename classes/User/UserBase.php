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
    
    public function __construct(string $table, bool $forceHttps = true)
    {
        if (Security\Url::getRequestMethod() === "GET")
        {
            exit('Forbidden to use $_GET as request method!');
        } else if (!Security\Url::isHttps($forceHttps))
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

    public function setHash(mixed $hashMethod = PASSWORD_DEFAULT) : void
    {
        self::$hash = new Security\Hash($hashMethod);
    }

    final public function setField(string $key, mixed $value, bool $encrypt = false) : void
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

    final public function getField(string $key) : mixed
    {
        return $this->database->getField($key);
    }

    protected function readParameters(array $keys) : array
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

    public function dataExists(mixed ...$keys) : bool
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

        //FORCE THE INDEX BACK TO 1
        //SET TO 0 AND THEN + 1
        $this->database->setIndex();
        $this->database->next();

        $this->database->prepare("SELECT * FROM `{$this->table}` WHERE {$whereClause}", ...array_values($param));

        //PREPARE WILL CAUSE THE INDEX TO RESET TO - 1 AFTER ITS FINISHED
        //FORCE THE INDEX BACK TO 1
        //SET TO 0 AND THEN + 1
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