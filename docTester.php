<?php
	require_once "autoloader.php";
		new DatabaseConnection\Connection("local", "localhost", "root", "", "testdb");
		new DatabaseConnection\Connection("master", "sql3.xel.nl", "vh86810-1", "#SaNdYmOvEs5000GezelLiG", "vh86810-1db1");
	if (isset($_GET["submit"]))
	{
		$server = "localhost";
		$name = "root";
		$pwd = "";
		$db = "vh86810-1db1";

		$obj = new DataHandler\Recordset("test");
		$obj->save();
	}
?>
<!DOCTYPE html>
<html>
<head>
	<title>test</title>
</head>
<body>
	<form>
		<input type="text" name="testVAR" id="asd"/>
		<input type="text" name="b" id="test"/>
		<button name="submit" type="submit">submit</button>
	</form>
</body>
</html>