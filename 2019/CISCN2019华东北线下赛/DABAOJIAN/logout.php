<?php
	session_start();
	$_SESSION = [];
	echo "<script>alert('登出成功！')</script>";
	header('location: index.php');
?>
