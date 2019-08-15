<?php
#用户名：admin，密码：五位数，check
$user="admin";
$pass="09938";

session_start();
header("Content-Type:text/html;charset=utf-8");
include_once('flag.php');
if (!isset($_POST['submit'])) {
    echo "<script>alert('非法访问！')</script>";
    exit();
}

if(isset($_POST['verifycode'])&&!empty($_SESSION['img_number'])&&$_POST['verifycode'] == $_SESSION['img_number']){
    if(isset($_POST['username'])&&isset($_POST['password']))
    {
    	$username = $_POST['username'];
    	$password = $_POST['password'];
    	if($user==$username&&$pass==$password) {
    		echo '<script>alert("登录成功");</script>';
            echo "flag is: ".getFlag();
    	}
    	else {
    		echo "<script>alert('用户名或密码错误');window.location.href='login.html'</script>";
    	}
    }
}else{
    echo "<script>alert('验证码错误');window.location.href='login.html'</script>";
}
