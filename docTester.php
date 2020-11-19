<?php
	require_once "autoloader.php";

	if (isset($_GET["submit"]))
	{
		new DatabaseConnection\Connection("local", "localhost", "root", "", "testdb");
		$obj = new User\Register("test");
		$_GET["testINT"] = $obj->encryptPassword($_GET["testINT"]);
		$obj->setParameters("testVAR");
		$obj->setValues("test");
		if (!$obj->dataExists())
		{
			$obj->save();
		}
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
		<input type="text" name="testINT" id="test"/>
		<button name="submit" type="submit">submit</button>
	</form>
</body>
</html>