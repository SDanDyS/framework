<?php
	//REGISTER AUTOLOADER
	spl_autoload_register(function($class) {
		
		//REPLACE BACKSLASH FOR FORWARD SLASH, THIS WAY IT BECOMES COMPATIBLE FOR ALL SYSTEMS
		$class = str_replace("\\", "/", $class);

		//LOAD USER FROM OUTSIDE FRAMEWORK DIRECTORY
		if (file_exists(__DIR__."/framework/classes/{$class}.php"))
		{
			require_once __DIR__."/framework/classes/{$class}.php";

			//LOAD USER FROM WITHIN THE FRAMEWORK DIRECTORY (TESTING PURPOSES)
		} else if (file_exists(__DIR__."/classes/{$class}.php"))
		{
			require_once __DIR__."/classes/{$class}.php";
		}
	});
?>