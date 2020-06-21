<?php
	namespace Files;
	class FilesController
	{
		private  $filePath;

		public function getUrlBase($path = NULL)
		{
			$base = $_SERVER['DOCUMENT_ROOT'];

			/*if (!$_SERVER["REMOTE_ADDR"] == "127.0.0.1" || !$_SERVER["REMOTE_ADDR"] == "::1")
			{
				$base = str_replace($_SERVER["DOCUMENT_ROOT"], $_SERVER["SERVER_NAME"], $base);
			}*/

			if (is_null($path))
			{
				return $base;
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
		private function setParams($path, $mode, $recursive = FALSE, $assigner = FALSE)
		{
			if (!is_string($path))
			{
				exit(__METHOD__."<br/> Argument <b>path</b> is not a string.");
			}
			if (!is_bool($recursive))
			{
				exit(__METHOD__."<br/> Argument <b>recursive</b> is not a boolean.");
			}

			$this->filePath["path"] = $this->getUrlBase($path);
			$this->filePath["mode"] = $mode;
			$this->filePath["recursive"] = $recursive;
			$this->filePath["assigner"] = $assigner;

			$this->createDirectoryOrFile();
		}


		public function createDirectory($path, $mode = 0777, $recursive = FALSE)
		{
			$this->setParams($path, $mode, $recursive, "DIR");
		}


		public function createFile($path, $mode = "w+", $recursive = FALSE)
		{
			$this->setParams($path, $mode, $recursive, "FILE");
		}


		public function getParams($extract = NULL)
		{
			if (is_null($extract))
			{
				return $this->filePath;
			} else if ($this->filePath[$extract])
			{
				return $this->filePath[$extract];
			} else 
			{
				exit(__METHOD__."<br/>Argument <b>{$extract}</b> value could not be retrieved.<br/> Available options:<br/> path<br/>mode<br/>recursive");
			}
		}


		public static function chmod($path, $mode)
		{
			chmod($path, $mode);
		}


		private function createDirectoryOrFile()
		{
			if ($this->filePath["assigner"] === "DIR")
			{
				if(!is_dir($this->filePath["path"]))
				{
					mkdir($this->filePath["path"], $this->filePath["mode"], $this->filePath["recursive"]);
				}
			} else if ($this->filePath["assigner"] === "FILE")
			{
				if (!is_file($this->filePath["path"]))
				{
					$file = fopen($this->filePath["path"], $this->filePath["mode"]);
					//CREATE A WRITE FUNCTION LATER
				}
			}
		}


		public function setDirectory($path)
		{
			$path = $this->getUrlBase($path);
			
			if (!is_dir($path))
			{
				exit(__METHOD__."<br/> The given path is not a directory.");
			}

			if (empty($this->filePath["path"]))
			{
				$filePermission = self::fileperms($path);
				$this->filePath["mode"] = $filePermission;
				$this->filePath["recursive"] = FALSE; 
			}
				$this->filePath["path"] = $path;
		}


		//order deny,allow
		//deny from all
		//allow from >>>INSERT YOUR ID<<<
		public function setDirectoryPermission($permission, $overwrite = FALSE)
		{
			$file = NULL;

			$permissionFile = $this->getParams("path")."/.htaccess";

			if(!is_bool($overwrite))
			{
				exit(__METHOD__."<br/>Parameter <b>overwrite</b> is not a boolean. Please set this to TRUE or FALSE");
			}
			if (!is_dir($this->getParams("path")))
			{
				exit("The given path is not a directory. Please set the directory you wish to set a permission for.");
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


		public static function fileperms($permissionOfFile)
		{
			return substr(sprintf('%o', fileperms($permissionOfFile)), -4);
		}


		public function deleteDirectory($path)
		{
			$path = $this->getUrlBase($path);

			if(is_dir($path))
			{
				unlink($path);
			}
		}

		public function deleteFile($path)
		{
			$path = $this->getUrlBase($path);

			if(is_file($path))
			{
				unlink($path);
			}
		}

		public function delete($path)
		{
			$path = $this->getUrlBase($path);
			
			unlink($path);
		}
	}

	$obj = new FilesController;
	$ob = $obj->setDirectory("framework/uploads");
	echo $ob;
	//$obj->setDirectory("framework/uploads");
	//FilesController::createFile("framework/uploads/t.php");
	//$obj->setDirectoryPermission("Allow from all", TRUE);
?>