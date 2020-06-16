<?php
	class FilesController
	{
		private static $filePath;

		public static function getUrlBase($path = NULL)
		{
			$base = $_SERVER['DOCUMENT_ROOT'];

			if (is_null($path))
			{
				return $path;
			}

			if (!$_SERVER["REMOTE_ADDR"] == "127.0.0.1" || !$_SERVER["REMOTE_ADDR"] == "::1")
			{
				$base = str_replace($_SERVER["DOCUMENT_ROOT"], $_SERVER["SERVER_NAME"], $base);
			}

			return "{$base}/{$path}";
		}

		/*
		* NOTICE:
		* The mode parameter consists of three octal number components specifying access restrictions for the owner,
		* the user group in which the owner is in, and to everybody else in this order. 
		* One component can be computed by adding up the needed permissions for that target user base. 
		* Number 1 means that you grant execute rights, number 2 means that you make the file writeable, number 4 means that you make the file readable.
		* Add up these numbers to specify needed rights.
		* EXAMPLE:
		* Read and writeable only would be: 2 + 4 = 6.
		* End result: 0600
		*/
		public static function setFilePath($path, $mode = 0777, $recursive = FALSE)
		{
			if (!is_string($path))
			{
				self::getSuppressionCaller(__METHOD__, $recursive);
			}
			if (!is_int($mode))
			{
				self::getSuppressionCaller(__METHOD__, $recursive);
			}
			if (!is_bool($recursive))
			{
				self::getSuppressionCaller(__METHOD__, $recursive);
			}

			self::$filePath["filePath"] = self::getUrlBase($path);
			self::$filePath["mode"] = $mode;
			self::$filePath["recursive"] = $recursive;
		}

		public static function getFilePath($extract = NULL)
		{
			if (is_null($extract))
			{
				return self::$filePath;
			} else if (self::$filePath[$extract])
			{
				return self::$filePath[$extract];
			} else 
			{
				self::getSuppressionCaller(__METHOD__, $extract);
			}
		}

		public static function chmod($filePath, $mode)
		{
			chmod($filePath, $mode);
		}

		//CREATE A FILE WRITE METHOD FOR .HTACCESS Deny from all / Allow from all
		//order deny,allow
		//deny from all
		//allow from >>>INSERT YOUR ID<<<
		public static function setDirectoryPermission($permission, $overwrite = FALSE)
		{
			$file = NULL;

			$permissionFile = self::getFilePath('filePath')."/.htaccess";

			if(!is_bool($overwrite))
			{
				exit(__METHOD__."<br/>Parameter <b>overwrite</b> is not a boolean. Please set this to TRUE or FALSE");
			}

			if(file_exists($permissionFile))
			{
				switch ($overwrite)
				{
					case TRUE:
						$file = fopen($permissionFile, "w+");
					break;

					case FALSE:
						$file = fopen($permissionFile, "a+");
					break;
				}
			} else
			{
				$file = fopen($permissionFile, "w+");
			}

			$content = file($permissionFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

			if (!in_array($permission, $content))
			{
				$permission = $permission."\n";

				fwrite($file, $permission);
				fclose($file);
			}
		}

		public static function unlink($path)
		{
			$path = self::getUrlBase($path);
			if(is_dir($path) || is_file($path))
			{
				echo "test";
				unlink($path);
			}
		}
	}
?>