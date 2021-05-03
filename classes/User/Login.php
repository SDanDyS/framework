<?php
namespace User;
use DataHandler\Recordset;
use Security\Hash;

class Login extends UserBase
{
    public function __construct($table, $forceHttps = true)
    {
        parent::__construct($table, $forceHttps);
    }
}
?>