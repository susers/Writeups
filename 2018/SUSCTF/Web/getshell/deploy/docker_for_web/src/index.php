<?php
show_source(__FILE__);
$mess=$_POST['mess'];
if(preg_match("/[a-zA-Z]/",$mess)){
	die("invalid input!");
}
eval($mess);
