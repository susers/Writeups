<?php
/**
 * Created by PhpStorm.
 * User: 5am3
 * Date: 2019/5/5
 * Time: 5:18 PM
 */
session_start();
include 'db.php';
include 'function.php';


if(isset($_POST['username']) && isset($_POST['password'])){
    $username=$_POST['username'];
    $password=$_POST['password'];

    if(register($username,$password,$conn)){

        $alertText = "注册成功，请登陆！";
        $alertType = "success";

    }else{
        $alertText = "用户名重复，或存在非法字符！";
        $alertType = "warning";
    }
}

?>


<html lang="en"><head>
    <meta charset="utf-8">
    <title>注册</title>
    <link href="/static/css/main.css" rel="stylesheet">
    <link href="https://cdn.bootcss.com/twitter-bootstrap/3.3.6/css/bootstrap.min.css" rel="stylesheet">

</head>

<body>

<div class="container">

    <form method="POST" role="form">
        <h2>注册</h2>
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
        <p>已有账号，前往<a src="./login.php" class='a-click'>登陆</a></p>
        <button type="submit" class="btn btn-default">注册</button>
    </form>

    <?php if(isset($alertText) && $alertText!= "") alert($alertText,$alertType);?>

</div>
<?php getJS()?>



</body></html>
