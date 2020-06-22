<?php
	/*
	* class Connection creates connections for the user
	* the user can save the connections this way and easily change the localhost to the master connection
	*/

	namespace DatabaseConnection;
	use \mysqli;
	class Connection
	{
		/*
		* $server stashes all the connections
		*/
		private static $host;
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
			self::setRemoteHost();
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

		public static function setConnection($key = NULL)
		{
			if (is_null($key))
			{	
				return self::getServer(self::getRemoteHost());
			} else
			{
				return self::getServer($key);
			}
		}

		public static function setRemoteHost()
		{

			if ($_SERVER["REMOTE_ADDR"] == "127.0.0.1" || $_SERVER["REMOTE_ADDR"] == "::1")
			{
				self::$host = "local";
			} else 
			{
				self::$host = "master";
			}	
			
		}

		public static function getRemoteHost()
		{
			return self::$host;
		}

	}

	//INST TEST CONNECTION
	new Connection("local", "localhost", "root", "", "testdb");
	new Connection("master", "localhost", "root", "", "testdb");
?>