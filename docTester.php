<?php

use User\Login;

require_once "autoloader.php";
		new DatabaseConnection\Connection("local", "localhost", "root", "", "testdb");
		new DatabaseConnection\Connection("master", "sql3.xel.nl", "vh86810-1", "#SaNdYmOvEs5000GezelLiG", "vh86810-1db1");
		$i = 0;
	if (isset($_POST["submit"]))
	{
		//$obj = new DataHandler\Recordset("test", false);
		$obj = new Login("test", false);
		$obj->setField("b", "a");
		$obj->setField("c", "zz");
		var_dump($obj->dataExists("b"));
		echo "<br/>";
		var_dump($obj->dataExists("c"));
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
	<form method="POST">
		<input type="text" name="b" id="asd"/>
		<input type="text" name="c" id="test"/>
		<button name="submit" type="submit">submit</button>
	</form>
</body>
</html>