<?php
	require_once "autoloader.php";
	if (isset($_GET["submit"]))
	{
		new DatabaseConnection\Connection("local", "localhost", "root", "", "testdb");
		$server = "localhost";
		$name = "root";
		$pwd = "";
		$db = "vh86810-1db1";
		$conn = new mysqli($server, $name, $pwd, $db);
		$obj = new DataHandler\Recordset("test");
		$obj->setField("b", "suck", true);
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