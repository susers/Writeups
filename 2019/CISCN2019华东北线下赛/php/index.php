<?php 
error_reporting(0);
$file=$_GET["file"];
highlight_file(__FILE__);

if(!is_array($file)){
	if (strpos(file_get_contents($file), "We1come_To_C1sCn")!==false){
		include($file);
	}else{
		echo "Give up!";
	}
}else{
	die("Give up Hacker!");
}

?>