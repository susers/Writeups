<?php
    include 'function.php';
    session_start();

    if(!isset($_SESSION['login']) || $_SESSION['login'] != 1) header('Location: /login.php');

    if(isset($_SESSION['code_nn'])){
        $_SESSION['code_o']=$_SESSION['code_nn'];

    }
    $_SESSION['code']=rand(1000000,9999999);
    $_SESSION['code_nn']=substr(md5($_SESSION['code']), 0, 6);

    $alertText = "hello，在这里，你可以发表文章。文章一旦被管理员审核通过后，可以在主页显示哦。<br/>p.s. 你可以通过提交反馈，来让管理员对你的文章进行审核。";
    $alertType = "success";

    if(isset($_POST['post'])){
        $alertText = "你的文章发表在了 ".savepost($_POST['post']);
        $alertType="success";
    }


?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>文章精选</title>
    <link href="/static/css/main.css" rel="stylesheet">
    <link href="https://cdn.bootcss.com/twitter-bootstrap/3.3.6/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    
<div class="container">
    <?php headers();?>
    <?php alert($alertText,$alertType);?>
       
    <div class="row-fluid">
        <div>
            <form method="POST">
                <div class="form-group" >
                     <label for="post">发表文章：</label>
                     <textarea class="form-control" rows="10" type="text" name="post" placeholder="hey,说点什么吧"></textarea> 
                </div>
                     <button type="submit" class="btn btn-default">提交</button>
                
            </form>
        </div>
    </div>


</div>
    <?php getJS();?>
</body>
</html>