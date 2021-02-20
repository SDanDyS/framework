<?php
	namespace DatabaseConnection;
	use \mysqli;
	/*
	* class Connection creates connections for the user
	* the user can save the connections this way and easily change the localhost to the master connection
	*/
	class Connection
	{
		/*
		* $server stashes all the connections
		*/
		private static $host;
		private static $server = [];

		public function __construct($target, $dbServer, $dbUserName, $dbPwd, $dbName)
		{
			self::setRemoteHost();

			self::$server[$target] = [$dbServer, $dbUserName, $dbPwd, $dbName];
		}

		private static function getServer($key)
		{
			if (array_key_exists($key, self::$server)) 
			{
				$databaseCredentials = self::$server[$key];

				$databaseConnection = new mysqli(...$databaseCredentials);

				if ($databaseConnection->connect_error) 
				{
					exit("Script exit. Database error: {$databaseConnection->connect_error}");
				}
				else 
				{
					return $databaseConnection;
				}
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
?>