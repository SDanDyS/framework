<?php
namespace User;
use DataHandler\Recordset;
use Security\Hash;

class Login extends UserBase
{
    public function __construct($table)
    {
        parent::__construct($table);
    }
}
?>