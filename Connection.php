<?php
	/*
	* class Connection creates connections for the user
	* the user can save the connections this way and easily change the localhost to the master connection
	*/
	class Connection
	{
		/*
		* $server stashes all the connections
		*/
		private static $server = [];

		public function __construct($server, $dbServer, $dbUserName, $dbPwd, $dbName)
		{

			$databaseConnection = new mysqli($dbServer, $dbUserName, $dbPwd, $dbName);

			if ($databaseConnection->connect_error) 
			{

				exit("Script exit. Database error: {$databaseConnection->connect_error}");

			}
			else 
			{

				self::$server[$server] = $databaseConnection;

			}

		}

		private static function getServer($key)
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

		public static function setConnection($key)
		{
			return self::getServer($key);
		}

	}

	//INST TEST CONNECTION
	new Connection("local", "localhost", "root", "", "testdb");
?>