<?php
/**
 * Created by PhpStorm.
 * User: EVatom
 * Date: 19/3/22
 * Time: 8:55
 */
error_reporting(0);
require_once('db.inc.php');
require_once('config.php');

if (isset($_POST['username']) && isset($_POST['password'])) {
    $stmt = $mysqli->prepare('select id,password,student_number from users where username=?');
    $username = $_POST['username'];
    $stmt->bind_param('s',$username);
    $bool = $stmt->execute();
    $stmt->store_result();
    if($stmt->num_rows<1){
        header('Location:login.php?status=nouser');
        die();
    }
    if($bool){
        $stmt->bind_result($id,$password, $student_number);
        $stmt->fetch();
        if ($password===md5($_POST['password'])){
            $_SESSION['login']=true;
            $_SESSION['id']=$id;
            $ip = $_SERVER["REMOTE_ADDR"];
            $iden = "student";
            $time = date('Y-m-d H:i:s');
            $stmt->close();
            $student_number = data_process($student_number);
            $query=$mysqli->query("insert into log values($id,'$student_number','$ip','$time','$iden');");
            if (!$query){
                header('Location:index.php?status=inserterror');
            }
			else{
				header('Location:index.php');
			}
        }
        else{
            header('Location:login.php?status=passerror');
        }
    }
    else{
        header('Location:login.php?status=error');
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>登陆</title>
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
                    <h3>登录</h3>
                <div class="a">
                    <input type="text" name="username" class="in-1" placeholder="请输入用户名">
                </div>
                <div class="a">
                    <input type="password" name="password" class="in-1" placeholder="请输入密码">
                </div>
                <div class="a">
                    <button type="submit">登录</button>
                </div>
                <div class="a">
                    <a href="register.php">点击注册</a>
                </div>
                <div class="a">
                    <?php
                    switch ($_GET['status']) {
                        case "nouser":
                            echo "用户不存在";
                            break;
                        case "passerror":
                            echo "密码错误";
                            break;
                        case "success":
                            echo "注册成功";
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