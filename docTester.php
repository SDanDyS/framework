<?php

use DatabaseConnection\Connection;
use DataHandler\Recordset;
use User\Authentication;
use Helper\Session;
use System\FileSystem;

		require_once "autoloader.php";
		 new DatabaseConnection\Connection("local", "localhost", "root", "", "testdb");
		 $conn = DatabaseConnection\Connection::setConnection();
		
		$i = 0;
	$t = new FileSystem();
	FileSystem::setAppRoot();
	FileSystem::setDocumentRoot();
	$t->setUploadsDirectory("uploads");
	Session::start();
	
	if (isset($_POST["submit"]))
	{
		$obj = new Recordset("test");
		$obj->setImageObject($t);
		$obj->save();
		exit();
	}
?>
<!DOCTYPE html>
<html>
<head>
	<title>test</title>
<!-- Latest compiled and minified CSS -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">

<!-- jQuery library -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

<!-- Popper JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>

<!-- Latest compiled JavaScript -->
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

<script src="https://kit.fontawesome.com/3cad08ea6d.js" crossorigin="anonymous"></script>

<script src="js/change.js"></script>
</head>
<body>
 <a href="test.php">test</a>
	<form method="POST" enctype="multipart/form-data">
		<input type="text" name="b" id="asd"/>
		<input type="file" name="c" id="test"/>
		<!-- <input type="text" name="token" value= -->
		<button name="submit" type="submit">submit</button>
	</form>
		<div id="tester" style="height: 500px;"></div>
	<!-- <script>
		getDirectoryList("classes/System/TreeView.php", "#tester", "C:/xampp/htdocs/framework/classes");
	</script> -->
</body>
</html>