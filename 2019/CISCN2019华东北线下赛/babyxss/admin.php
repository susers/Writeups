<?php
/**
 * Created by PhpStorm.
 * User: 5am3
 * Date: 2019/5/5
 * Time: 5:48 PM
 */

session_start();
include 'function.php';
include "db.php";
if(isset($_SESSION['uid']) && $_SESSION['uid'] === 1) $admin=1;
else $admin =0;

if($admin === 1 &&isset($_GET['id'])) {
    $user = getSecret($_GET['id'],$conn);
    if(strstr($user,"5am3")){
        $alertText="抱歉，不能查询管理员哦！";
        $alertType="warning";
    }else{
        $alertText="你查询的用户是：".$user;
        $alertType="success";

    }

}



$strs= <<<strs
<form method="GET" role="form">
       
        <div class="form-group" >
            <label>请输入要查询用户的id</label>
            <div class="input-group">
                <div class="input-group-addon">用户ID</div>
                <input class="form-control"  type="text" name="id" placeholder="请输入ID。" />
            </div>
        </div>
        
        <button type="submit" class="btn btn-default">查询</button>
    </form>
strs;

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
        <div class="header clearfix">
            <nav>
                <ul class="nav nav-pills pull-right">
                    <li role="presentation"><a href="/">主页</a></li>
                    <li role="presentation"><a href="post.php">投稿</a></li>
                    <li role="presentation"><a href="commitbug.php">反馈</a></li>
                    <li role="presentation"><a href="about.php">关于我</a></li>
                    <?php if($admin===1) echo "<li role=\"presentation\"><a href=\"admin.php\">管理面板</a></li>";  ?>
                </ul>
            </nav>
            <h3 class="text-muted">文章精选</h3>
        </div>

        <?php
        if($admin===1){
            echo $strs;
            echo "<br/>";
            if(isset($alertText)){

                alert($alertText,$alertType);
            }


        }else{
            $alertText="你不是管理员哦，这里不给你看！︿(￣︶￣)︿";
            $alertType="warning";
            alert($alertText,$alertType);

        }

        ?>

    </div>
    <?php getJS();?>
</body>
</html>
