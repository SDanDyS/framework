<?php
namespace User;
use DataHandler\Recordset;
use Security\Hash;

class Register extends UserBase
{
    public function __construct($table, $forceHttps = true)
    {
        parent::__construct($table, $forceHttps);
    }

    public function save() 
    {
        $this->database->save();
    }
}
?>