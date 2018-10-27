<?php

    class File{

        private $typelist;
        private $allowexten;
        private $path;

        function __construct(){
            
            if (!isset($_SESSION['username'])){
                exit("not login");
            }
            $this->typelist==array("image/jpeg","image/jpg","image/png","image/gif");
            $this->notallow=array("php", "php5", "php3", "php4", "php7", "pht", "phtml", "htaccess","html", "swf", "htm");
            $this->path='./upload';
        }

        function save(){
            
            $id=$_SESSION['id'];
            $upfile=$_FILES['pic'];
            $fileinfo=pathinfo($upfile["name"]);
            if(in_array($fileinfo["extension"],$this->notallow)){
                exit('error');
            }
            $path='./upload/'.$id."_".$fileinfo["filename"].".".strtolower($fileinfo["extension"]);
            if (file_exists($path)){
                exit("file already exists");
            }
            if(move_uploaded_file($upfile['tmp_name'],  $path)){
                //return True;
                return $path;
            }else{
                return False;
            }
        }
    }
?>
