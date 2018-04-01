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
$hostname="localhost";
$dbuser="web";
$dbpass="web";
$database="demo2";
$mysqli=new mysqli($hostname,$dbuser,$dbpass,$database);
session_start();
