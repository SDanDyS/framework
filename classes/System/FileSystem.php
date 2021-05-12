<?php
    namespace System;
    class FileSystem
    {
        private static $DOCUMENT_ROOT;
        private static $filePermission = 0777;
        private static $filePath = null;
        private static $dirPath = null;

        public function __construct()
        {
            self::documentRoot();
        }

        private static function documentRoot()
        {
			$base = $_SERVER['DOCUMENT_ROOT'];
            $differental = __DIR__;

            $explodedBase = explode("/", $base);
            $explodedDifferental = explode("\\", $differental);

            $totalCount = count($explodedBase) + 1;

            self::$DOCUMENT_ROOT = "";

            for ($i = 0; $i < $totalCount; $i++)
            {
                self::$DOCUMENT_ROOT .= "{$explodedDifferental[$i]}/";
            }
        }

		public static function getUrlBase(string $path = NULL)
		{

			if (is_null($path))
			{
				return self::$DOCUMENT_ROOT;
			}

			return self::$DOCUMENT_ROOT.$path;
		}

        public static function writeFile(string $path, mixed $msg = "", bool $overwrite = false)
        {
            //order deny,allow
            //deny from all
            //allow from >>>INSERT YOUR ID<<<
            //https://www.inmotionhosting.com/support/edu/website-design/access-specific-filetype/

            /*
                Order Allow,Deny
                Deny from all		
                <FilesMatch ".(jpg|gif|png)$">
                Order Deny,Allow
                    Allow from all
                </FilesMatch>
            */

            switch($overwrite)
            {
                case true:
                    file_put_contents($path, $msg);
                    break;
                case false:
                    file_put_contents($path, $msg, FILE_APPEND);
                    break;
            }
        }

        public static function mkdir(string $path, int $mode = 0777, bool $recursive = false)
        {
            if (!is_dir($path))
            {
                if (!mkdir($path, $mode, $recursive)) 
                {
                    exit("Failed to create directory...");
                }
            }
        }

        public static function getFileContent(string $path)
        {
            return file_get_contents($path);
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
        public static function chmod(string $path, int $mode)
		{
			chmod($path, $mode);
		}

		public static function setConstantFilePermission(int $mode)
		{
			self::$filePermission = $mode;
		}

        public static function getConstantFilePermission()
		{
			return self::$filePermission;
		}

        public static function fileperms(string $permissionOfFile)
		{
            //CONVERT TO OCTAL NUMBER. EASIER TO READ FOR USER
			return substr(sprintf('%o', fileperms($permissionOfFile)), -4);
		}

        //IMPLEMENTED, BUT DISCOURAGED TO USE. CREATES OVERHEAD
		public static function delete(mixed &$request)
		{
			unset($request);
		}

        public static function deleteFile(string $path, bool $customPath = false)
		{
            if ($customPath)
            {
                $location = $path;
            } else
            {
                $location = FileSystem::getUrlBase().$path;
            }

			if(is_file($location))
			{
				unlink($location);
			}
		}

        public static function deleteDir(string $path, bool $customPath = false)
        {
            if ($customPath)
            {
                $location = $path;
            } else
            {
                $location = FileSystem::getUrlBase().$path;
            }

            if (is_dir($location)) 
            { 
                $objects = scandir($location);

                foreach ($objects as $object) 
                { 
                    if ($object != "." && $object != "..") 
                    { 
                        if (is_dir($location.DIRECTORY_SEPARATOR.$object) && !is_link($location.DIRECTORY_SEPARATOR.$object))
                        {
                            self::deleteDir($location.DIRECTORY_SEPARATOR.$object, true);
                        } else
                        {
                            unlink($location.DIRECTORY_SEPARATOR.$object); 
                        }
                    }
                }
                rmdir($location); 
            } 
        }
    }
?>