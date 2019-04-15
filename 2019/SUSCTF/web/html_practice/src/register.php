<?php
/**
 * Created by PhpStorm.
 * User: Machenike
 * Date: 19/3/22
 * Time: 9:55
 */
error_reporting(0);
require_once ('db.inc.php');
if (isset($_POST['username']) && isset($_POST['password']) && isset($_POST['student_number'])){
    $stmt = $mysqli->prepare('select password from users where username=?');
    $stmt->bind_param('s', $_POST['username']);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows !=0){
        header('Location:register.php?status=already');
        die();
    }
    if(is_numeric($_POST['student_number'])) {
        $stmt = $mysqli->prepare("insert into users(username,password,student_number) values(?,?,?)");
        $stmt->bind_param('sss',$_POST['username'], md5($_POST['password']), $_POST['student_number']);
        $res = $stmt->execute();
        if (!$res) {
            header('Location:register.php?status=error');
            die();
        }
        else{
            $_SESSION['id'] = $stmt->insert_id;
            header('Location:login.php?status=success');
        }
    }
    else{
        header('Location:register.php?status=not_num');
        die();
    }
}

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>注册</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<script language="JavaScript">
    function chk()
    {
        var b=document.getElementsByTagName("input")
        for(var i=0;i<b.length;i++){
            if(b[i].value==""){
                alert("输入不能为空！");
                b[i].focus();
                return false;
            }
        }
        return true;
    }
</script>
<div class="wel" id="background-3"></div>
<div class="wel" id="box">
    <div class="box-1 lefp"></div>
    <div class="box-1">
        <div class="righp"></div>
    </div>
</div>
<div class="wel" id="git"></div>
<div class="wel" id="from">
    <div class="box-2 le-1">
        <form action="#" method="post" onsubmit="return chk()">
            <div class="flrg">
                <h3>注册</h3>
                <div class="a">
                    <input type="text" name="username" class="in-1" placeholder="请输入账号">
                </div>
                <div class="a">
                    <input type="password" name="password" class="in-1" placeholder="请输入密码">
                </div>
                <div class="a">
                    <input type="text" name="student_number" class="in-1" placeholder="请输入学号">
                </div>
                <div class="a">
                    <button type="submit">注册</button>
                </div>
                <div class="a">
                    <a href="login.php">去登陆</a>
                </div>
                <div class="a">
                    <?php
                    switch ($_GET['status']) {
                        case "already":
                            echo "用户已存在";
                            break;
                        case "error":
                            echo "出错";
                            break;
                        case "not_num":
                            echo "学号必须输入数字";
                            break;
                        case "incomplete":
                            echo "信息不完整";
                            break;
                    }
                    ?>
                </div>
            </div>
        </form>
    </div>
</div>
</body>
</html>
