<!DOCTYPE html>
<html>
<head>
	<title>loginlogin</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
</head>
<body>
<?php
error_reporting(0);
session_start();
require 'conf.php';

$filter = "count|procedure|and|ascii|substr|substring|left|right|union|if|case|regexp|pow|exp|order|sleep|benchmark|outfile|dumpfile|load_file|join|-";

$username = (isset($_POST['username']) === true && $_POST['username'] !== '') ? (string)$_POST['username'] : die('Missing username');
$password = (isset($_POST['password']) === true && $_POST['password'] !== '') ? (string)$_POST['password'] : die('Missing password');

function random($length){   
   $output='';   
   for ($a = 0; $a<$length; $a++) {   
       $output .= chr(mt_rand(33, 126)); 
    }   
    return $output;   
}   

if((preg_match("/".$filter."/is",$username)== 1) || (preg_match("/".$filter."/is",$password)== 1)){
	echo "<hr/>"."<h1 style='text-align:center'>为什么要这样对我 T_T，你认为你能得到你想要的吗.</h1>";
	die();
}
else{
	$sql="SELECT p0ssw0rd FROM userinfo WHERE username = '$username'";
	$query = mysqli_query($con,$sql); 
	if (mysqli_num_rows($query) == 1) { 
		$key = mysqli_fetch_array($query);
	    if($key['p0ssw0rd'] == $password) {
	    	$co = random(32);
			setcookie('token',md5(md5($username.md5($co))), time()+2*60*60);
			setcookie('cookie',md5($co), time()+2*60*60);
			header('location:successfull.php');
	    }else{
	        echo "<hr/>"."<h1 style='text-align:center'>账号或密码错误 T_T </h1>";
	    }
	}
	else{
		echo "<hr/>"."<h1 style='text-align:center'>账号或密码错误 T_T</h1>";
	} 
}
mysqli_close($con);
?>
</body>
</html>
