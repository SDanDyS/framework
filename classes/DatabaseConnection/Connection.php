<?php
	namespace DatabaseConnection;
	use \mysqli;
	use \PDO;
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
			$databaseCredentials = self::getServerCredentials($key);

			//MYSQLI CONNECTION
			$databaseConnection["mysqli"] = new mysqli(...$databaseCredentials);

			if ($databaseConnection["mysqli"]->connect_error) 
			{
				exit("Script exit. Database error: {$databaseConnection["mysqli"]->connect_error}");
			}

			//PDO CONNECTION
			$dsn = "mysql:host={$databaseCredentials[0]};dbname={$databaseCredentials[3]};charset=utf8mb4";
			$options = [
				PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
				PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
				PDO::ATTR_EMULATE_PREPARES   => false,
			];

			try {
				$databaseConnection["pdo"] = new PDO($dsn, $databaseCredentials[1], $databaseCredentials[2], $options);
		   } catch (\PDOException $e) {
				throw new \PDOException($e->getMessage(), (int)$e->getCode());
		   }

		   //BOTH CONNECTIONS SUCCEEDED, SEND BACK AND LET USER DECIDE WHETHER THEY NEED MYSQLI OR PDO
			return $databaseConnection;
		}

		private static function getServerCredentials(string $key)
		{
			if (array_key_exists($key, self::$server))
			{
				$databaseCredentials = self::$server[$key];
				return $databaseCredentials;
			}
			exit("Script exit. Error: server {$key} could not be found and retrieved.");
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