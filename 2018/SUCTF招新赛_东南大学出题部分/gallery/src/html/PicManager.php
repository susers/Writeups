<?php

 class PicManager{
	 private $current_dir;
	 private $whitelist=['.jpg','.png','.gif'];
	 private $logfile='request.log';
	 private $actions=[];

	 public function __construct($dir){
		 $this->current_dir=$dir;
		 if(!is_dir($dir))@mkdir($dir);
	 }

	 private function _log($message){
		 array_push($this->actions,'['.date('y-m-d h:i:s',time()).']'.$message);
	 }

	 public function pics(){
		 $this->_log('list pics');
		 $pics=[];
		 foreach(scandir($this->current_dir) as $item){
			 if(in_array(substr($item,-4),$this->whitelist))
				 array_push($pics,$this->current_dir."/".$item);
		 }
		 return $pics;
	 }
	 public function upload_pic(){
		 $this->_log('upload pic');
		 $file=$_FILES['file']['name'];
		 if(!in_array(substr($file,-4),$this->whitelist)){
			 $this->_log('unsafe deal:upload filename '.$file);
			 return;
		 }
		 $newname=md5($file).substr($file,-4);
		 move_uploaded_file($_FILES['file']['tmp_name'],$this->current_dir.'/'.$newname);
	 }
	 public function get_pic($picname){
		 $this->_log('get pic');
		 if(!file_exists($picname))
			 return '';
		 $fi=new finfo(FILEINFO_MIME_TYPE);
		 $mime=$fi->file($picname);
		 header('Content-Type:'.$mime);
		 return file_get_contents($picname);
	 }

	 public function clean(){
		 $this->_log('clean');
		 foreach(scandir($this->current_dir) as $file){
			 @unlink($this->current_dir."/".$file);
		 }
	 }
	 public function __destruct(){
		 $fp=fopen($this->current_dir.'/'.$this->logfile,"a");
		 foreach($this->actions as $act){
			fwrite($fp,$act."\n");
		 }
		 fclose($fp);
	 }


 }

//$pic=new PicManager('./');
//$pic->gen();

