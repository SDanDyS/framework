<?php
namespace User;
use DataHandler\Recordset;
use Encryption\Encryption;
class Register
{
    private $databaseAccess;
    private $table;
    private static $debugger;
    private $values;
    private $params;
    
    
    public function __construct($table)
    {
        $this->table = $table;
        $this->databaseAccess = new Recordset($table);
    }

    public static function debugger($debuggingTools = true)
    {
        ini_set('error_log', './errors.log');
        //error_reporting(E_ALL);
        self::$debugger = $debuggingTools;
    }

    public static function getDebuggerStatus()
    {
        return self::$debugger;
    }

    public function hash($data, $hash = null)
    {
        if (is_null($hash))
        {
            $encrypt = new Encryption();
        } else
        {
            $encrypt = new Encryption($hash);
        }

        return $encrypt->hash($data);
    }

    public function save()
    {
        $this->databaseAccess->save();
    }

    public function setParameters(...$params)
    {
        $this->params = $params;
    }

    public function setValues(...$values)
    {
        $this->values = $values;
    }

    private function selectQuery()
    {
        $query = "SELECT * FROM `{$this->table}` WHERE";

        if (count($this->params) === 1)
        {
            $query = $query . " {$this->params[0]} = ?";
        } else
        {
            $parameterCount = count($this->params);

            foreach ($this->params as $k => $v)
            {
                if ($parameterCount - 1 === $k)
                {
                    $query = $query . " {$v} = ?";
                } else
                {
                    $query = $query . " {$v} = ? AND";
                }
            }
        }
        $this->databaseAccess->prepare($query, ...$this->values);
    }

    public function dataExists()
    {
        if (empty($this->params))
        {
            exit("Error: No <b>parameters</b> passed along. This prevents a clauses on which the query should fire.");
        } else if (empty($this->values))
        {
            exit("Error: No <b>values</b> passed along. This prevents a clauses on which the query should fire.");
        } else if (count($this->params) !== count($this->values))
        {
            exit("The amount of parameters and values do not match. The amount of parameters given: {count($this->params)}<br/> The amount of values given: {count($this->values)}");
        } else
        {
            $this->selectQuery();

            $recordset = $this->databaseAccess->getField($this->databaseAccess->getPrimaryKey());
            if (empty($recordset))
            {
                return false;
            } else 
            {
                return true;
            }
        }
    }
}
?>