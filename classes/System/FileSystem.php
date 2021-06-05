<?php
    namespace System;
    class FileSystem
    {
        private static $APP_ROOT;
        private static $DOC_ROOT;
        private static $filePermission = 0777;
        const APPLICATION_ROOT = "APP_ROOT";
        const DOCUMENT_ROOT = "DOC_ROOT";

        public function __construct()
        {
            self::setAppRoot();
            self::$DOC_ROOT = $_SERVER['DOCUMENT_ROOT'];
        }

        private static function setAppRoot() : void
        {
			$base = $_SERVER['DOCUMENT_ROOT'];
            $differental = __DIR__;

            $explodedBase = explode("/", $base);
            $explodedDifferental = explode("\\", $differental);

            $totalCount = count($explodedBase) + 1;

            self::$APP_ROOT = "";

            for ($i = 0; $i < $totalCount; $i++)
            {
                self::$APP_ROOT .= "{$explodedDifferental[$i]}/";
            }
        }

		public static function getAppRoot(string $path = NULL) : string
		{

			if (is_null($path))
			{
				return self::$APP_ROOT;
			}

			return self::$APP_ROOT.$path;
		}

        public static function getDocumentRoot()
        {
            return self::$DOC_ROOT;
        }

        public static function writeFile(string $path, mixed $msg = "", bool $overwrite = false) : void
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

        public static function mkdir(string $path, int $mode = 0777, bool $recursive = false) : bool
        {
            if (!is_dir($path))
            {
                return mkdir($path, $mode, $recursive);
            }

            return false;
        }

        public static function getFileContent(string $path) : string
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
        public static function chmod(string $path, int $mode) : void
		{
			chmod($path, $mode);
		}

		public static function setConstantFilePermission(int $mode) : void
		{
			self::$filePermission = $mode;
		}

        public static function getConstantFilePermission() : int
		{
			return self::$filePermission;
		}

        public static function fileperms(string $permissionOfFile) : int
		{
            //CONVERT TO OCTAL NUMBER. EASIER TO READ FOR USER
			return substr(sprintf('%o', fileperms($permissionOfFile)), -4);
		}

        //IMPLEMENTED, BUT DISCOURAGED TO USE. CREATES OVERHEAD
		public static function delete(mixed &$request) : void
		{
			unset($request);
		}

        public static function deleteFile(string $path, bool $customPath = false) : bool
		{
            if ($customPath)
            {
                $location = $path;
            } else
            {
                $location = FileSystem::getAppRoot().$path;
            }

			if(is_file($location))
			{
				return unlink($location);
			}

            return false;
		}

        public static function deleteDir(string $path, bool $customPath = false) : bool
        {
            if ($customPath)
            {
                $location = $path;
            } else
            {
                $location = FileSystem::getAppRoot().$path;
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
                return rmdir($location);
            }

            return false;
        }
    }
?>