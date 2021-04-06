<?php
	require_once "autoloader.php";
		new DatabaseConnection\Connection("local", "localhost", "root", "", "testdb");
		new DatabaseConnection\Connection("master", "sql3.xel.nl", "vh86810-1", "#SaNdYmOvEs5000GezelLiG", "vh86810-1db1");
		var_dump(Helper\Session::init());
		$_SESSION["id"] = session_id();
		var_dump($_SESSION["id"]);
	if (isset($_GET["submit"]))
	{
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
 <a href="test.php">test</a>
	<form>
		<input type="text" name="testVAR" id="asd"/>
		<input type="text" name="b" id="test"/>
		<button name="submit" type="submit">submit</button>
	</form>
</body>
</html>