<?php

use DataHandler\Recordset;
use User\Authentication;
use Helper\Session;
use System\FileSystem;

require_once "autoloader.php";
		new DatabaseConnection\Connection("local", "localhost", "root", "", "testdb");
		new DatabaseConnection\Connection("master", "sql3.xel.nl", "vh86810-1", "#SaNdYmOvEs5000GezelLiG", "vh86810-1db1");
		$i = 0;
	new FileSystem();
	Session::start();
	
	if (isset($_POST["submit"]))
	{
		$obj = new Recordset("test");
		$obj::setImageObject(new FileSystem());
		$obj->save();
		exit();
	}
?>
<!DOCTYPE html>
<html>
<head>
	<title>test</title>
</head>
<body>
 <a href="test.php">test</a>
	<form method="POST" enctype="multipart/form-data">
		<!-- <input type="text" name="b" id="asd"/> -->
		<input type="file" name="c" id="test"/>
		<!-- <input type="text" name="token" value= -->
		<button name="submit" type="submit">submit</button>
	</form>
</body>
</html>