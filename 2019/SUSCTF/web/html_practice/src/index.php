<?php
/**
 * Created by PhpStorm.
 * User: Machenike
 * Date: 19/3/21
 * Time: 21:26
 */
error_reporting(0);
require_once('db.inc.php');
if(!isset($_SESSION['login'])){
    header('Location:login.php');
}
if($_GET['status']=='inserterror')
	echo "插入数据异常！"

?>



<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">

		<title>东南大学HTML练习中心</title>

		<link rel="stylesheet" href="http://libs.baidu.com/bootstrap/3.0.3/css/bootstrap.min.css" />
	</head>
	<body>
		<nav class="navbar navbar-inverse navbar-fixed-top">
			<div class="container">
		    	<div class="navbar-header">
		    		<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
		            	<span class="sr-only">Toggle navigation</span>
		            	<span class="icon-bar"></span>
		            	<span class="icon-bar"></span>
		            	<span class="icon-bar"></span>
		          	</button>
		          	<a class="navbar-brand" href="#">Project name</a>
		        </div>
		        <div id="navbar" class="collapse navbar-collapse">
		          	<ul class="nav navbar-nav">
		            	<li class="active"><a href="./change.php">修改信息</a></li>
		            	<li class="active"><a href="./log.php">查看登陆记录</a></li>
		            	<li class="active"><a href="./practice.php">HTML练习</a></li>
                        <li class="active"><a href="./logout.php">退出登录</a></li>
		          	</ul>
		        </div>
		    </div>
		</nav>

		<div class="container" style="margin-top: 50px">
            <h1>欢迎使用东南大学HTML练习中心</h1>
            <p>点击上方标签，开始练习</p>
		</div>
		<script src="http://code.jquery.com/jquery-latest.js" />
		<script src="http://libs.baidu.com/bootstrap/3.0.3/js/bootstrap.min.js" />
	</body>
</html>
