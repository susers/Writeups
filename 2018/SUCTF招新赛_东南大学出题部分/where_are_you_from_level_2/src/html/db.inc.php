<?php
/**
 * Created by PhpStorm.
 * User: image
 * Date: 18-3-17
 * Time: 下午1:00
 */
function create_password($pw_length=10)
{ 
	$randpwd = ”; 
	for ($i = 0; $i < $pw_length; $i++) 
	{ 
		$randpwd .= chr(mt_rand(33, 126)); 
	} 
	return $randpwd; 
} 

function getIp(){
	if(!empty($_SERVER['HTTP_CLIENT_IP'])){
		$cip=$_SERVER['HTTP_CLIENT_IP'];
	}
	elseif(!empty($_SERVER['HTTP_X_FORWARDED_FOR'])){
		$cip=$_SERVER['HTTP_X_FORWARDED_FOR'];
	}
	elseif(!empty($_SERVER['REMOTE_ADDR'])){
		$cip=$_SERVER['REMOTE_ADDR'];
	}
	else{
		$cip='';
	}
	$cip=preg_replace('/\s|select|from|limit|union|join/iU','',$cip);
	return $cip;
}

$hostname="localhost";
$dbuser="web";
$dbpass="web";
$database="demo2";
$mysqli=new mysqli($hostname,$dbuser,$dbpass,$database);
session_start();
