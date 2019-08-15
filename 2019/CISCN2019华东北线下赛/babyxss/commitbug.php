<?php
    include 'function.php';
    session_start();
    if(!isset($_SESSION['login']) || $_SESSION['login'] != 1) header('Location: /login.php');

    if(isset($_SESSION['code_nn'])){
        $_SESSION['code_o']=$_SESSION['code_nn'];
    }
    $_SESSION['code']=rand(1000000,9999999);
    $_SESSION['code_nn']=substr(md5($_SESSION['code']), 0, 6);

    $alertText="感谢您对本网站的喜爱，我们会努力做得更好。谢谢反馈！";
    $alertType="success";

    if(isset($_POST['check'])){
        if(substr(md5($_POST['check']),0,6)===$_SESSION['code_o']){
            if(isset($_POST['bug'])){
                $url=$_POST['bug'];
                $pa="/^http:\/\/[a-zA-Z0-9\.\?\/=:]+$/";
                if($test = preg_match($pa, $url, $arr)){
                    savebug($_POST['bug']);
                    $alertText = '成功发送，我稍后将会阅读您的反馈！';
                    $alertType = "success";
                }else{
                    $alertText = '您的输入有误，请重新输入地址！';
                    $alertType = "warning";
                }


            }

        }else{
            $alertText = '验证码错误，请核实后再试！';
            $alertType = "warning";
        }

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
        

    <div>
        <form method="POST" role="form"> 
            <div class="form-group" >
                    <label for="bug" class="control-label">反馈内容：</label>
                    <div class="input-group">
                        <div class="input-group-addon">URL</div>
                        <input class="form-control"  type="text" name="bug" placeholder="请输入有问题的网址。我会亲自查看。" />
                    </div>
            </div>
            <div class="form-group">
                <label for="check">substr(md5($str), 0, 6) === “<?php echo $_SESSION['code_nn'];?>”：</label>
                <div class="input-group">
                    <div class="input-group-addon">验证码</div>
                    <input class="form-control" type="text" name="check">
                </div>  
            </div>
            
            <button type="submit" class="btn btn-default">提交</button>
            
        </form>


</div>
</div>
    <?php getJS();?>
</body>
</html>
