<?php
	function check_login(){
		session_start();
		if(!isset($_SESSION['name']) || !isset($_SESSION['money'])){
			//没有登录就转跳到登录界面
			header("location:register.php");
			die("请先登录@@@");
		}
	}
?>
