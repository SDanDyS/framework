<?php
	namespace Files;
	class FilesController
	{
		private $filePath;

		private static $baseDir;

		private static $filePermission;

		public static function getUrlBase($path = NULL)
		{
			$base = $_SERVER['DOCUMENT_ROOT'];

			if (is_null($path))
			{
				return $base;
			}

			return "{$base}/{$path}";
		}

		public static function setBaseDir($baseDir)
		{
			$baseDir = self::getUrlBase($baseDir);

			if (!is_dir($baseDir))
			{
				exit("The base directory given is not a directory.");
			}

			self::$baseDir = "{$baseDir}/";
		}

		public static function getBaseDir()
		{
			return self::$baseDir;
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

			$this->filePath["path"] = self::getBaseDir().$path;
			$this->filePath["mode"] = $mode;
			$this->filePath["recursive"] = $recursive;
			$this->filePath["assigner"] = $assigner;

			$this->createDirectoryOrFile();
		}


		public function createDir($path, $mode = 0777, $recursive = FALSE)
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

		public static function setFilePermission($mode)
		{
			self::$filePermission = $mode;
		}

		public static function getFilePermission()
		{
			return self::$filePermission;
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
					fclose($file);
				}
			}
		}


		public function setDir($path)
		{
			$path = self::getBaseDir().$path;
			
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
		//https://www.inmotionhosting.com/support/edu/website-design/access-specific-filetype/
		public function setDirectoryPermission($permission, $overwrite = FALSE)
		{
			$file = NULL;

			if(!is_bool($overwrite))
			{
				exit(__METHOD__."<br/>Parameter <b>overwrite</b> is not a boolean. Please set this to TRUE or FALSE");
			}

			if (empty($this->getParams("path")))
			{
				if(empty(self::getBaseDir()))
				{
					exit(__METHOD__."<br/>No directory could be targetted. <br/>There is no base directory nor a target directory.");
				} else if (!is_dir(self::getBaseDir()))
				{
					exit(__METHOD__."<br/>Base directory could not be found.<br/> Input: " .self::getBaseDir());
				} else 
				{
					$permissionFile = self::getBaseDir()."/.htaccess";
				}
			} else
			{
				if (!is_dir($this->getParams("path")))
				{
					exit("The given path is not a directory. Please set the directory you wish to set a permission for.");
				}

				$permissionFile = $this->getParams("path")."/.htaccess";
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


		public static function deleteDir($path)
		{
			$path = self::getBaseDir().$path;

			if(is_dir($path))
			{
				unlink($path);
			}
		}

		public static function deleteFile($path)
		{
			$path = self::getBaseDir().$path;

			if(is_file($path))
			{
				unlink($path);
			}
		}

		public static function delete($request)
		{
			unset($request);
		}
	}
?>