<?php
namespace User;
use DataHandler\Recordset;
use Security\Hash;
use Helper\Session;

class Authentication extends UserBase
{
    public function __construct(string $table, bool $forceHttps = true)
    {
        parent::__construct($table, $forceHttps);
    }

    public function login()
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
    }

    public function verify(mixed $key, mixed $differentInput = null)
    {
        if (is_null($differentInput))
        {
            $validPwd = self::$hash->verify($_POST[$key], $this->database->getField($key));

            if ($validPwd)
            {
                if (self::$hash->IsOldHash($this->database->getField($key)))
                {
                    $this->setField($key, $_POST[$key], true);
                    $this->database->save();
                }                
            }

            return $validPwd;
        } else
        {
            $validPwd = self::$hash->verify($differentInput, $this->database->getField($key));

            if ($validPwd)
            {
                if (self::$hash->IsOldHash($this->database->getField($key)))
                {
                    $this->setField($key, $differentInput, true);
                    $this->database->save();
                }                
            }

            return $validPwd;
        }
    }

    public function confirm()
    {
        Session::start();
        Session::set("Auth", $this);
    }

    public function logout()
    {
        Session::destroy();
    }
}
?>