<?php
/**
 * Created by PhpStorm.
 * User: image
 * Date: 18-3-17
 * Time: 下午1:02
 */
require_once('db.inc.php');
if(!isset($_SESSION['login'])){
    header('Location:login.php');
}
else{
    $stmt=$mysqli->prepare("select * from users where id=?");
    $stmt->bind_param('i',$_SESSION['id']);
    $res=$stmt->execute();
    if(!$res){
        header("Location:index.php?message=error");
    }
    $stmt->bind_result($id,$username,$password,$nickname,$age,$description);   
    $stmt->fetch();
    $_SESSION['token']=md5(create_password(20));
}
?>
<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>个人信息</title>

    <!-- CSS -->
    <link rel="stylesheet" href="http://fonts.googleapis.com/css?family=Roboto:400,100,300,500">
    <link rel="stylesheet" href="assets/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/font-awesome/css/font-awesome.min.css">
    <link rel="stylesheet" href="assets/css/form-elements.css">
    <link rel="stylesheet" href="assets/css/style.css">

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
    <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->

    <!-- Favicon and touch icons -->
    <link rel="shortcut icon" href="assets/ico/favicon.png">
    <link rel="apple-touch-icon-precomposed" sizes="144x144" href="assets/ico/apple-touch-icon-144-precomposed.png">
    <link rel="apple-touch-icon-precomposed" sizes="114x114" href="assets/ico/apple-touch-icon-114-precomposed.png">
    <link rel="apple-touch-icon-precomposed" sizes="72x72" href="assets/ico/apple-touch-icon-72-precomposed.png">
    <link rel="apple-touch-icon-precomposed" href="assets/ico/apple-touch-icon-57-precomposed.png">

</head>

<body>

<!-- Top content -->
<div class="top-content">

    <div class="inner-bg">
        <div class="container">
            <div class="row">
                <div class="col-sm-8 col-sm-offset-2 text">
                    <h1><strong>个人信息</strong> Personal info</h1>
                    <div class="description">

                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-6 col-sm-offset-3 form-box">
                    <div class="form-top">
                        <div class="form-top-left">
                            <h1>Personal infomation</h1>

                        </div>
                        <div class="form-top-right">
                            <i class="fa fa-key"></i>
                        </div>
                    </div>
                    <div class="form-bottom">
                        <form role="form" action="update.php" method="post" class="form-horizontal">

                            <div class="form-group">
                                <label for="firstname" class="col-sm-2 control-label" style="text-align: left;">用户名</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" id="firstname" placeholder="" value="<?php echo $username;?>">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="nickname" class="col-sm-2 control-label" style="text-align:left;">昵称</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" id="nickname" name="nickname" placeholder="昵称" value="<?php echo $nickname;?>" required="true">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="age" class="col-sm-2 control-label" style="text-align:left;">年龄</label>
                                <div class="col-sm-10">
                                    <input type="number" class="form-control" id="age" name="age" placeholder="请输入年龄" value="<?php echo $age;?>" required="true">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="description" class="col-sm-2">个人介绍</label>
                                <div class="col-sm-10"><textarea class="form-control" id="description" name="description" rows="3" required="true"><?php echo htmlentities($description);?></textarea></div>
			    </div>
				<input type="hidden" name="token" value="<?php echo $_SESSION['token'];?>">
                            <?php if(isset($_GET['message'])){
                                ?>
                                <div class="alert alert-danger alert-dismissable">
                                    <button type="button" class="close" data-dismiss="alert"
                                            aria-hidden="true">
                                        &times;
                                    </button>
                                    <?php
                                    if($_GET["message"]=="error") {
                                        echo "更新错误";
                                    }
				    elseif($_GET['message']=="csrf"){
					    echo "CSRF token mismatch";
				    }
				    else{
                                        echo "未知错误";
                                    }
                                    ?>
                                </div>
                            <?php } ?>
                            <div class="form-group">
                                <div class="col-sm-offset-2 col-sm-10">
                                <button type="submit" class="btn btn-success" style="width:45%;float:left;">更新资料</button>
                                    <a type="submit" class="btn btn-danger" style="width:45%;float:right;" href="logout.php">登出</a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-6 col-sm-offset-3 social-login">
                    <h3>Share our site with:</h3>
                    <div class="social-login-buttons">
                        <a class="btn btn-link-1 btn-link-1-facebook" href="#">
                            <i class="fa fa-facebook"></i> Facebook
                        </a>
                        <a class="btn btn-link-1 btn-link-1-twitter" href="#">
                            <i class="fa fa-twitter"></i> Twitter
                        </a>
                        <a class="btn btn-link-1 btn-link-1-google-plus" href="#">
                            <i class="fa fa-google-plus"></i> Google Plus
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
<div class="copyrights">Collect from <a href="http://www.cssmoban.com/"  title="网站模板">网站模板</a></div>


<!-- Javascript -->
<script src="assets/js/jquery-1.11.1.min.js"></script>
<script src="assets/bootstrap/js/bootstrap.min.js"></script>
<script src="assets/js/jquery.backstretch.min.js"></script>
<script src="assets/js/scripts.js"></script>

<!--[if lt IE 10]>
<script src="assets/js/placeholder.js"></script>
<![endif]-->

</body>

</html>
