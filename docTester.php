<?php

use User\Login;

require_once "autoloader.php";
		new DatabaseConnection\Connection("local", "localhost", "root", "", "testdb");
		new DatabaseConnection\Connection("master", "sql3.xel.nl", "vh86810-1", "#SaNdYmOvEs5000GezelLiG", "vh86810-1db1");
		var_dump(Helper\Session::init());
	if (isset($_POST["submit"]))
	{
		$obj = new Login("test", false);
		$obj->setField("b", "dd");
		$obj->setField("c", "zz");
		var_dump($obj->dataExists("c"));
		echo "<br/>";
		var_dump($obj->dataExists("b"));
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