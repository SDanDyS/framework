<?php
	/*
	* class Connection creates connections for the user
	* the user can save 
	*/
	class Connection
	{
		private static $server = [];

		public function __construct($server, $dbServer, $dbUserName, $dbPwd, $dbName)
		{

			$databaseAcceptance = new mysqli($dbServer, $dbUserName, $dbPwd, $dbName);

			if ($databaseAcceptance->mysqli_errno) 
			{

				exit("Script exit. Database error: {$databaseAcceptance->connect_error}");

			}
			else 
			{

				self::$server[$server] = new mysqli($dbServer, $dbUserName, $dbPwd, $dbName);

			}

		}

		public static function getServer($key)
		{

			if (array_key_exists($key, self::$server)) 
			{

				return self::$server[$key];

			}
			else 
			{

				exit("Script exit. Error: server {$key} could not be found and retrieved.");

			}

		}

	}
?>