<?php
/**
 * Created by PhpStorm.
 * User: 5am3
 * Date: 2019/5/5
 * Time: 5:15 PM
 */
session_start();
include 'db.php';
include 'function.php';

if(isset($_GET['logout'])) $_SESSION=array();
if(isset($_SESSION['login']) && $_SESSION['login'] == 1) header('Location: /');

if(isset($_POST['username']) && isset($_POST['password'])){
    $username = $_POST['username'];
    $password = $_POST['password'];
    $id = login($username,$password,$conn);
    if($id!=0){
        $_SESSION['login'] = 1;
        $_SESSION['uid'] = $id;
        $_SESSION['username'] = $username;

        $alertText = "<script>window.location.href='/';</script>";
        $alertType = "success";

    }else{
        $alertText = "登陆失败！请核实账号密码。";
        $alertType = "warning";
    }
}

?>


<html lang="en"><head>
    <meta charset="utf-8">
    <title>登陆</title>
    <link href="/static/css/main.css" rel="stylesheet">
    <link href="https://cdn.bootcss.com/twitter-bootstrap/3.3.6/css/bootstrap.min.css" rel="stylesheet">

</head>

<body>

<div class="container">

    <form method="POST" role="form">
        <h2>登录</h2>
        <hr>
        <div class="form-group" >
            <div class="input-group">
                <div class="input-group-addon">用户名</div>
                <input class="form-control"  type="text" name="username" placeholder="请输入用户名。" />
            </div>
        </div>
        <div class="form-group">

            <div class="input-group">
                <div class="input-group-addon">密&nbsp;&nbsp;&nbsp;码&nbsp;</div>
                <input class="form-control" type="text" name="password">
            </div>
        </div>
        <p>暂无账号，前往<a src="./register.php" class='a-click'>注册</a></p>

        <button type="submit" class="btn btn-default">登陆</button>
    </form>

    <?php if(isset($alertText) && $alertText!= "") alert($alertText,$alertType);?>

</div>
<?php getJS()?>



</body></html>
