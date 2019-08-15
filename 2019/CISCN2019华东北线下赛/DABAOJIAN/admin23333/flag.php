<?php
	//获取flag
	$FLAG = file_get_contents("/flag");
	function getFlag(){
	    global $FLAG;
	    echo $FLAG;
	}
?>
