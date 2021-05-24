<?php
namespace User;
use DataHandler\Recordset;
use Security\Hash;

class Register extends UserBase
{
    public function __construct(string $table, bool $forceHttps = true)
    {
        parent::__construct($table, $forceHttps);
    }

    public function save() : void
    {
        $this->database->save();
    }
}
?>