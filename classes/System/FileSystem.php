<?php
    namespace System;
    class FileSystem
    {
        private static $DOCUMENT_ROOT;
        private static $filePermission = 0777;

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
		public static function getUrlBase($path = NULL)
		{

			if (is_null($path))
			{
				return self::$DOCUMENT_ROOT;
			}

			return self::$DOCUMENT_ROOT.$path;
		}

        public static function writeFile()
        {

        }

        public static function chmod($path, $mode)
		{
			chmod($path, $mode);
		}

		public static function setConstantFilePermission($mode)
		{
			self::$filePermission = $mode;
		}

        public static function getConstantFilePermission()
		{
			return self::$filePermission;
		}

        public static function fileperms($permissionOfFile)
		{
            //CONVERT TO OCTAL NUMBER. EASIER TO READ FOR USER
			return substr(sprintf('%o', fileperms($permissionOfFile)), -4);
		}

        //IMPLEMENTED, BUT DISCOURAGED TO USE. CREATES OVERHEAD
		public static function delete(&$request)
		{
			unset($request);
		}

        public static function deleteFile($path, $customPath = false)
		{
            if ($customPath)
            {
                $location = $path;
            } else
            {
                $location = self::$DOCUMENT_ROOT.$path;
            }

			if(is_file($location))
			{
				unlink($location);
			}
		}

        public static function deleteDir($path, $customPath = false)
		{
            if ($customPath)
            {
                $location = $path;
            } else
            {
                $location = self::$DOCUMENT_ROOT.$path;
            }

            // if (!is_dir($location)) 
            // {
            //     exit("$location must be a directory");
            // }
            if (substr($location, strlen($location) - 1, 1) != '/') {
                $location .= '/';
            }
            $files = str_replace("\\\\", "", glob($location . '*', GLOB_MARK));
            foreach ($files as $file) {
                if (is_dir($file)) {
                    self::deleteDir($file, true);
                } else {
                    unlink($file);
                }
            }
            rmdir($location);
		}
    }

    echo $_SERVER["DOCUMENT_ROOT"];
	echo "<br/>";
    new FileSystem();
	echo FileSystem::getUrlBase("uploads");
    
?>