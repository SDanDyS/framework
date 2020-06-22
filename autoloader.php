<?php
	spl_autoload_register(function($class) {

		//$class = str_replace("\\", "/", $class);

		if (file_exists(__DIR__."\\framework\\classes\\{$class}.php"))
		{
			require_once __DIR__."\\framework\\classes\\{$class}.php";
		}
	});
?>