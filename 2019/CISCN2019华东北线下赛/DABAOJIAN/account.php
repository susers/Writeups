<!DOCTYPE html>
<html lang="zh-CN">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="/favicon.ico">
    <title>一个简简单单的网站</title>
    <link href="static/css/bootstrap.min.css" rel="stylesheet">
  </head>
  <?php
    include_once('check_login.php');
    check_login();
  ?>
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
            <li class="active"><a href="account.php">账号中心</a></li>
          </ul>
        </div><!--/.nav-collapse -->
      </div>
    </nav>

    <div class="container" style="margin:70px 0px 0px 0px">
		<h4>体验金用完了，可以尝试注册其他账号呀！</h4>
		<br>
		<br>
        <?php
            $username = $_SESSION['name'];
            $money = $_SESSION['money'];
            echo "<h5>当前登录的账号是：$username </h5><br>";
            echo "<h5>当前账号的余额是：$money </h5><br>";
        ?>
		<a href="logout.php"><button type="submit" class="btn btn-default">退出</button></a>
    </div>
    <script src="static/js/jquery.min.js"></script>
    <script src="static/js/bootstrap.min.js"></script>
  </body>
</html>
