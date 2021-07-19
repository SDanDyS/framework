<?php
namespace System;
class Treeview 
{
 
    private $files = [];
    private $folder;
     
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
 
   public function createTree() 
   {
             
        if (count($this->files) > 2) 
        { 
            /* First 2 entries are . and ..  -skip them */
            natcasesort($this->files);

            $list = '<ul class="filetree" style="display: none;">';
           
            // Group folders first
            foreach ($this->files as $file) 
            {
                if (file_exists($this->folder.$file) && $file != '.' && $file != '..' && is_dir($this->folder.$file)) 
                {
                    $list .= '<li class="folder collapsed"><a href="#" rel="' . htmlentities($this->folder.$file) . '/">' . htmlentities($file) . '</a></li>';
                }
            }
            // Group all files
            foreach ($this->files as $file) 
            {
                if (file_exists($this->folder.$file) && $file != '.' && $file != '..' && !is_dir($this->folder.$file)) 
                {
                    $ext = preg_replace('/^.*\./', '', $file);
                    $list .= '<li class="file ext_' . $ext . '"><a href="#" rel="' . htmlentities($this->folder.$file) . '">' . htmlentities($file) . '</a></li>';
                }
            }
            $list .= '</ul>'; 
            return $list;
        }
   }
}

$tt = new Treeview($_POST['dir']);
//  exit($path);
echo $tt->createTree();
?>