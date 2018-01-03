<?php
	error_reporting(0);
	session_start();
	$zmkm = $_SESSION["zmkm"];
	if(empty($zmkm)){
		header("Location: login.php");
		exit();
	}
	
	if(!empty($_POST)){
		if($_POST['key'] == 'fuckyou'){
			echo 'xx';//flag
			exit();
		}
	}

?>
<html lang="zh-CN">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="../../favicon.ico">
    <title>内部系统</title>
    <link href="./css/bootstrap.min.css" rel="stylesheet">
    <link href="./css/signin.css" rel="stylesheet">
	<script type="text/javascript" src="./js/jquery.min.js"></script>
  </head>
  <body>
    <div class="container">
      <h1>hello word</h1>
    </div>
	
  </body>
</html>


