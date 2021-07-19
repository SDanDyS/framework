<?php
namespace System;
class Treeview 
{
 
    private array $files = [];
    private string $folder;

    private string $directoryStyling;
    private string $fileStyling;

    private static string $staticDirectoryStyling;
    private static string $staticFileStyling;
     
   public function __construct($path) 
   {
         
        //file name or directory exists
        if(file_exists($path)) 
        {
            //substract one, because array is 0 based indexed
            if($path[strlen($path) - 1] ==  '/' )
            {
                $this->folder = $path;
            } else
            {
                //does not have directory seperator, add directory seperator
                $this->folder = $path . '/';
            }
             
            $this->dir = opendir($path);

            while (($file = readdir($this->dir)) != false)
            {
                $this->files[] = $file;
            }

            closedir($this->dir);
        }
   }
 
   public function createTree() : string
   {
             
        if (count($this->files) > 2) 
        { 
            /* First 2 entries are . and ..  -skip them */
            natcasesort($this->files);

            $directoryStyling = $this->getDirectoryFontAwesome();

            $fileStyling = $this->getFileFontAwesome();

            $list = "<ul class='filetree' style='display: none;'>";

            // Group folders first
            foreach ($this->files as $file) 
            {
                if (file_exists($this->folder.$file) && $file != '.' && $file != '..' && is_dir($this->folder.$file)) 
                {
                    $list .= "<li class='folder collapsed'><span class='{$directoryStyling}'></span><a href='#' rel='".htmlentities($this->folder.$file)."'>" . htmlentities($file) . "</a></li>";
                }
            }

            // Group all files
            foreach ($this->files as $file) 
            {
                if (file_exists($this->folder.$file) && $file != '.' && $file != '..' && !is_dir($this->folder.$file)) 
                {
                    $ext = preg_replace("/^.*\./", "", $file);
                    $list .= "<li class='file ext_'{$ext}'><span class='{$fileStyling}'></span><a href='#' rel='".htmlentities($this->folder.$file)."'>" . htmlentities($file) . "</a></li>";
                }
            }
            $list .= "</ul>";
            return $list;
        }
   }

   public function setDirectoryFontAwesome(string $styling, bool $isStatic = false) : void
   {
       if (!$isStatic)
       {
           $this->directoryStyling = $styling;
       } else
       {
           self::$staticDirectoryStyling = $styling;
       }
   }

   public function getDirectoryFontAwesome()
   {
        if (!empty($this->directoryStyling))
        {
            $directoryStyling = $this->directoryStyling;
        } else if (!empty(self::$staticDirectoryStyling))
        {
            $directoryStyling = self::$staticDirectoryStyling;
        } else
        {
            $directoryStyling = "";
        }

        return $directoryStyling;
   }

   public function setFileFontAwesome(string $styling, bool $isStatic = false) : void
   {
       if (!$isStatic)
       {
           $this->fileStyling = $styling;
       } else
       {
           self::$staticFileStyling = $styling;
       }
   }

   public function getFileFontAwesome() : string
   {
        if (!empty($this->fileStyling))
        {
            $fileStyling = $this->fileStyling;
        } else if (!empty(self::$staticFileStyling))
        {
            $fileStyling = self::$staticFileStyling;
        } else
        {
            $fileStyling = "";
        }

        return $fileStyling;
   }
}

$tt = new Treeview($_POST['dir']);
$tt->setDirectoryFontAwesome("fas fa-folder");
echo $tt->createTree();
?>