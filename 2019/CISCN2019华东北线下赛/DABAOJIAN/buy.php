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
            <li  class="active"><a href="buy.php">购买中心</a></li>
            <li><a href="withdraw.php">兑换中心</a></li>
            <li><a href="account.php">账号中心</a></li>
          </ul>
        </div><!--/.nav-collapse -->
      </div>
    </nav>

    <div class="container" style="margin:30px 0px 0px 0px">
		<div class="page-header">
			<h2>彩票中心<small>      买买买买买，才能使你变强大</small></h2>
		</div>
		<div class="panel panel-primary">
			<div class="panel-heading">
				福利彩票
			</div>

			<div class="panel-body">
				<form method="post">
					<div class="input-group">
						<span class="input-group-addon" id="basic-addon1">NUM</span>
						<input type="text" id="numbers" class="form-control" name="numbers" minlength="6" maxlength="6" pattern="\d{6}" required placeholder="输入6位数字">
					</div>
                    <button type="button" id="submit_but" class="btn btn-primary">购买</button>
				</form>
			</div>
		</div>
        <div class="panel panel-success" id="result" style="display: none;">
            <div class="panel-heading">
                <h4>开奖结果
                    <span class="label label-danger">
                        <span id="same_counter"></span>
                    </span>
                </h4>
                <h5>账号余额:<span id="money"></span> | 奖金:<span id="win_money"></span></h5>
            </div>
            <div class="panel-body">
                <div class="alert alert-success" role="alert">中奖号码
                    <div id="win_nums">
                    </div>
                </div>
                <div class="alert alert-info" role="alert">你的号码
                    <div id="user_number">
                </div>
            </div>
        </div>
    </div>


</div>
    <script src="static/js/jquery.min.js"></script>
    <script src="static/js/bootstrap.min.js"></script>
    <script type="text/javascript" src="static/js/buy_ajax.js"></script>
  </body>
</html>
