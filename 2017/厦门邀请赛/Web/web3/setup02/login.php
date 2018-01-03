<?php
	if(!empty($_POST)){
		if($_POST['username'] == 'xx' && $_POST['password'] == 'xx'){//生产环境密码不同
			session_start();
			$_SESSION["zmkm"] = md5($_POST['password']);
			header("Location: index.php");
			exit();
		}
	}
?>
<!DOCTYPE html>
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

      <form class="form-signin" action="login.php" method="post">
        <h2 class="form-signin-heading">登入内部系统</h2>
        <label for="username" class="sr-only">用户名</label>
        <input type="text" id="username" name="username" class="form-control" placeholder="用户名" required autofocus>
        <label for="password" class="sr-only">密码</label>
        <input type="password" id="password" name="password" class="form-control" placeholder="密码" required>
        <button class="btn btn-lg btn-primary btn-block" type="submit">登入</button>
      </form>
    </div>
	
  </body>
</html>
