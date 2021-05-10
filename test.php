<?php
require_once "autoloader.php";
Helper\Session::start();
var_dump(Helper\Session::get("Auth")->getField("b"));
// var_dump(Helper\Session::init());
//     var_dump($_SESSION["id"]);

phpinfo();
?>