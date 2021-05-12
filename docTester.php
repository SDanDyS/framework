<?php

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
		// $obj = new User\Register("test", false);
		// $obj->setField("b", $_POST["b"]);
		// $obj->setField("c", "FIRED", true);
		// $obj->save();
		
		// $ob = new User\Authentication("test", false);
		// $ob->setField("b", $_POST["b"]);
		// $_POST["c"] = "FIRED";
		// $ob->login();
		// if($ob->verify("c"))
		// {
		// 	$ob->confirm();
		// }
		// $ob->logout();
		// var_dump(Session::get("Auth"));
		Security\CSRF::validateToken();
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
		<input type="text" name="b" id="asd"/>
		<!-- <input type="file" name="c" id="test"/> -->
		<input type="text" name="token" value=<?php echo Security\CSRF::generateToken(); ?>>
		<button name="submit" type="submit">submit</button>
	</form>
</body>
</html>