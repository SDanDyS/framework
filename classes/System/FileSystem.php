<?php
    namespace System;
    class FileSystem
    {
        private static $DOCUMENT_ROOT;

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
    }

    echo $_SERVER["DOCUMENT_ROOT"];
	echo "<br/>";
    new FileSystem();
	echo FileSystem::getUrlBase("uploads");
    
?>