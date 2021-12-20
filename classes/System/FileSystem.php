<?php
    namespace System;

use Directory;

class FileSystem
    {
        // REPLICATE CONSTANTS
        private static array $ROOT = [
            "APP_ROOT" => "",
            "DOCUMENT_ROOT" => ""
        ];
        private string $targetDirectory;
        private string $uploadsDirectory;
        private int $filePermission = 0777;

        public function __construct(string $targetRoot = "APP_ROOT", int $filePermission = 0777)
        {
            /**
             * One or more config settings have not been set
             * Instantiate it, to prevent errors
             */
            if (empty(self::$ROOT["APP_ROOT"]) || empty(self::$ROOT["DOCUMENT_ROOT"]))
            {
                self::setAppRoot();
                self::setDocumentRoot();
            }
            $this->targetDirectory = $targetRoot;
            $this->filePermission = $filePermission;
        }

        public function setUploadsDirectory(string $directoryName, bool $strict = false) : void
        {
            if (!is_dir(self::$ROOT[$this->targetDirectory].$directoryName) && $strict)
            {
                exit("Could not find directory named: {$directoryName}. <br/> Root: {$this->targetDirectory}. <br/> Did you make the directory?");
            }

            self::mkdir(self::$ROOT[$this->targetDirectory].$directoryName);

            if (is_dir(self::$ROOT[$this->targetDirectory].$directoryName))
            {
                $this->uploadsDirectory = "{$directoryName}/";
            } else
            {
                exit("Directory could not be made! <br/> Path: " .self::$ROOT[$this->targetDirectory].$directoryName);
            }
            $this->uploadsDirectory = "{$directoryName}/";
        }

        public function getUploadsDirectory() : string
        {
            if (empty($this->uploadsDirectory))
            {
                return "";
            }
            return $this->uploadsDirectory;
        }

        public function getTargetRoot() : string
        {
            return self::$ROOT[$this->targetDirectory];
        }

        public static function setDocumentRoot() : void
        {
            self::$ROOT["DOC_ROOT"] = $_SERVER['DOCUMENT_ROOT']."/";
        }

        public static function setAppRoot() : void
        {
			$base = $_SERVER['DOCUMENT_ROOT'];
            $differental = __DIR__;

            $explodedBase = explode("/", $base);
            $explodedDifferental = explode("\\", $differental);

            $totalCount = count($explodedBase) + 1;

            self::$ROOT["APP_ROOT"] = "";

            for ($i = 0; $i < $totalCount; $i++)
            {
                self::$ROOT["APP_ROOT"] .= "{$explodedDifferental[$i]}/";
            }
        }

		public static function getAppRoot(string $path = NULL) : string
		{
            if (empty(self::$ROOT["APP_ROOT"]))
            {
                exit("Set the APPLICATION ROOT by calling the method <b>setAppRoot</b> prior to using <b>getAppRoot</b>");
            }

			if (is_null($path))
			{
				return self::$ROOT["APP_ROOT"];
			}

			return self::$ROOT["APP_ROOT"].$path;
		}

        public static function getDocumentRoot(string $path = NULL) : string
        {
            if (empty(self::$ROOT["DOC_ROOT"]))
            {
                exit("Set the DOCUMENT ROOT by calling the method <b>setDocumentRoot</b> prior to using <b>getDocumentRoot</b>");
            }

            if (is_null($path))
			{
				return self::$ROOT["DOC_ROOT"];
			}

			return self::$ROOT["DOC_ROOT"].$path;
        }

        public static function writeFile(string $path, mixed $msg = NULL, bool $overwrite = false) : void
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

            if (is_null($msg))
            {
                $msg = "";
            }
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

		public function setConstantFilePermission(int $mode) : void
		{
			$this->filePermission = $mode;
		}

        public function getConstantFilePermission() : int
		{
			return $this->filePermission;
		}

        public static function fileperms(string $fileName) : int
		{
            //CONVERT TO OCTAL NUMBER. EASIER TO READ FOR USER
			return substr(sprintf('%o', fileperms($fileName)), -4);
		}

        //IMPLEMENTED, BUT DISCOURAGED TO USE. CREATES OVERHEAD
		public static function delete(mixed &$request) : void
		{
			unset($request);
		}

        public static function deleteFile(string $path) : bool
		{

            $location = $path;

			return unlink($location);
		}

        public static function deleteDir(string $path) : bool
        {
        
            $location = $path;

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