<?php

use User\Login;

require_once "autoloader.php";
		new DatabaseConnection\Connection("local", "localhost", "root", "", "testdb");
		new DatabaseConnection\Connection("master", "sql3.xel.nl", "vh86810-1", "#SaNdYmOvEs5000GezelLiG", "vh86810-1db1");
		$i = 0;
	if (isset($_POST["submit"]))
	{
		$uploads = new Files\FilesController;
		Files\FilesController::setBaseDir("framework");
		$uploads->createDir("uploads/", 0666);
		$uploads->setDir("uploads");
		Files\FilesController::setFilePermission(0666);
		$uploads->setDirectoryPermission('Order Allow,Deny
	Deny from all
	<FilesMatch ".(jpg|jpeg|jpe|gif|png|bmp|tif|ico)$">
		Order Deny,Allow
		Allow from all
	</FilesMatch>', true);
		// $obj = new DataHandler\Recordset("test");
		// $obj::setImageObject($uploads);
		// $obj->save();
		// $obj = new Login("test", false);
		// $obj->setField("b", "a");
		// $obj->setField("c", "zz");
		// var_dump($obj->dataExists("b"));
		// echo "<br/>";
		// var_dump($obj->dataExists("c"));
		// exit();
		$obj = new User\Register("test", false);
		$obj->setField("b", $_POST["b"]);
		$obj->setField("c", "FIRED", true);
		$obj->save();
	}

	// echo $_SERVER["DOCUMENT_ROOT"];
	// echo "<br/>";
	// echo __DIR__;
	require_once "classes/System/FileSystem.php";
?>
<!DOCTYPE html>
<html>
<head>
	<title>test</title>
</head>
<body>
 <a href="test.php">test</a>
	<form method="POST" enctype="multipart/form-data">
		<input type="text" name="b" id="asd"/>
		<!-- <input type="file" name="c" id="test"/> -->
		<button name="submit" type="submit">submit</button>
	</form>
</body>
</html>