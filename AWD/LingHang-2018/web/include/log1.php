<?php
	
	// $path = "/var/www/html/log.txt";
	$path = "/var/www/html/log.php";
	$fp = fopen($path,"a+");
	$str = "IP ".$_SERVER["REMOTE_ADDR"]." url:".'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']." ";
	$str .= "Gets:".json_encode($_GET)." POST:".json_encode($_POST)." COOKIE:".json_encode($_COOKIE)."HEADER: ".json_encode($_SERVER)."\r\n";
	fwrite($fp, $str);
?>
