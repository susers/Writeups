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
  <?php
    ini_set('display_errors', 1);
    require_once('check_login.php');
    check_login()
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
          <a class="navbar-brand" href="index.php">福利彩票</a>
        </div>
        <div id="navbar" class="collapse navbar-collapse">
          <ul class="nav navbar-nav">
            <li><a href="index.php">首页</a></li>
            <li><a href="buy.php">购买中心</a></li>
			<li class="active"><a href="withdraw.php">兑换中心</a></li>
            <li><a href="account.php">账号中心</a></li>
          </ul>
        </div><!--/.nav-collapse -->
      </div>
    </nav>

    <div class="container" style="margin:60px 0px 0px 0px">
		<div class="jumbotron">
			<h1>购买大宝剑一枚</h1>
			<p>只要100000，大宝剑带回家</p>
			<p><a class="btn btn-primary btn-lg" href="withdraw.php?action=dbj" role="button">购买</a></p>
		</div>
		<div class="jumbotron">
			<h1>购买提示一枚</h1>
			<p>RTRTRT，售价只要9998</p>
			<p><a class="btn btn-primary btn-lg" href="withdraw.php?action=ts" role="button">购买</a></p>
		</div>
		<div class="jumbotron">
			<h1>提现</h1>
			<p>RTRTRT</p>
			<p><a class="btn btn-primary btn-lg" href="withdraw.php?action=tx" role="button">提现</a></p>
		</div>
    </div>

	<?php
		// session_start();
		if($_SERVER['REQUEST_METHOD']=='GET'){
			if(isset($_GET['action'])){
				$action = $_GET['action'];
				if($action === 'dbj'){
					$money = $_SESSION['money'];
					if($money > 100000){
						$money -= 100000;
						$_SESSION['money'] = $money;
						echo "<script>alert('购买大宝剑成功！但是我们的彩票中心倒闭了，老板带着小姨子跑了。欠我们的工资，欠我们的血汗钱，我们也没办法，又不能删库跑路是吧。所以我偷偷把网站后台给你，那里才有真的大宝剑！后台：/admin23333/login.html，用户名：admin，密码才是5位数的数字。')</script>";
					}else{
						echo "<script>alert('骚年,你支付不起大宝剑的钱！')</script>";
						// header("location:withdraw.php");
					}
				}elseif($action === 'ts'){
					$money = $_SESSION['money'];
					if($money>9998){
						$money -= 9998;
						$_SESSION['money'] = $money;
						echo "<script>alert('提示1:php弱类型了解一下！')</script>";
					}else{
						echo "<script>alert('骚年,你支付不起购买提示的钱！')</script>";
					}
				}elseif ($action === 'tx') {
					echo "<script>alert('别想勒，现在不支持提现！')</script>";
				}else{
					echo "<script>alert('操作异常')</script>";
					// header("location:withdraw.php");
				}
			}
		}else{
			echo '<script>alert("操作异常！")</script>';
		}
	?>
    <script src="static/js/jquery.min.js"></script>
    <script src="static/js/bootstrap.min.js"></script>
  </body>
</html>
