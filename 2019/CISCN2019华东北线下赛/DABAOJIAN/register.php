<!DOCTYPE html>
<html lang="zh-CN">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="../../favicon.ico">

    <title>一个简简单单的网站</title>
    <link href="static/css/bootstrap.min.css" rel="stylesheet">
  </head>
  <body>

    <nav class="navbar navbar-inverse navbar-fixed-top">
      <div class="container">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
            <span class="sr-only">福利彩票</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand" href="">福利彩票</a>
        </div>
        <div id="navbar" class="collapse navbar-collapse">
          <ul class="nav navbar-nav">
            <li><a href="index.php">首页</a></li>
            <li><a href="buy.php">购买中心</a></li>
			<li><a href="withdraw.php">兑换中心</a></li>
            <li><a href="account.php">账号中心</a></li>
          </ul>
        </div><!--/.nav-collapse -->
      </div>
    </nav>

    <div class="container" style="margin:70px 0px 0px 0px">
		<h4>注册即送50RMB代金券，随便注册吧，对了密码放这只是为了好看而已，没有什么用的！</h4>
		<br>
		<br>
		<form method="post" action="">
			<div class="form-group">
				<label for="exampleInputEmail1">用户名</label>
					<input type="text" class="form-control" id="" name="username" placeholder="UserName">
			</div>
			<div class="form-group">
				<label for="exampleInputEmail1">密  码</label>
					<input type="password" class="form-control" id="" name="password" placeholder="Password">
			</div>
			<button type="submit" class="btn btn-default">注册</button>
		</form>
    </div>
    <script src="static/js/jquery.min.js"></script>
    <script src="static/js/bootstrap.min.js"></script>
	<?php
		if(!isset($_POST['username'])){
			echo "<script>alert('进入注册界面！')</script>";
		}
		ini_set('display_errors', 1);
		if($_SERVER['REQUEST_METHOD']=='POST'){
			session_start();
			$username = $_POST['username'];
			$password = $_POST['password'];
			$_SESSION['name'] = $username;
			$_SESSION['money'] = 50;
			header('location:index.php');
		}else{
			echo isset($_POST['username']);
		}
	?>
  </body>
</html>
