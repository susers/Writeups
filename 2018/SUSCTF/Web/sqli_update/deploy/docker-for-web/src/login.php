<?php
/**
 * Created by PhpStorm.
 * User: image
 * Date: 18-3-17
 * Time: 下午1:10
 */
require_once ('db.inc.php');
if(isset($_POST['username']) && isset($_POST['password'])){
	if($_POST['token']!=$_SESSION['token']){
		header('Location:login.php?message=csrf');
		die('CSRF token mismatch');
    	}
    $stmt=$mysqli->prepare('select id,password from users where username=?');
    $stmt->bind_param('s',$_POST['username']);
    $stmt->execute();
    $stmt->store_result();
    if($stmt->num_rows<1){
        header('Location:login.php?message=nouser');
        die();
    }
    $stmt->bind_result($id,$password);
    $stmt->fetch();
    if(md5($_POST['password'])!=$password){
        header('Location:login.php?message=errorpass');
        die();
    }
    $_SESSION['login']=true;
    $_SESSION['id']=$id;
    header('Location:index.php');
}
else{
	$_SESSION['token']=md5(create_password(20));
}
?>
<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login</title>

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
                    <h1><strong>登录</strong> Login Form</h1>
                    <div class="description">

                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-6 col-sm-offset-3 form-box">
                    <div class="form-top">
                        <div class="form-top-left">
                            <h3>Login to our site</h3>
                            <p>Enter your username and password to log on:</p>
                        </div>
                        <div class="form-top-right">
                            <i class="fa fa-key"></i>
                        </div>
                    </div>
                    <div class="form-bottom">
                        <form role="form" action="" method="post" class="login-form">
                            <div class="form-group">
                                <label class="sr-only" for="username">Username</label>
                                <input type="text" name="username" placeholder="Username..." class="form-username form-control" id="form-username">
                            </div>
                            <div class="form-group">
                                <label class="sr-only" for="password">Password</label>
                                <input type="password" name="password" placeholder="Password..." class="form-password form-control" id="form-password">
			    </div>
				<input type="hidden" value="<?php echo $_SESSION['token'];?>" name="token">
                            <?php if(isset($_GET['message'])){
                                ?>
                                <div class="alert alert-danger alert-dismissable">
                                    <button type="button" class="close" data-dismiss="alert"
                                            aria-hidden="true">
                                        &times;
                                    </button>
                                    <?php
                                    if($_GET["message"]=="nouser") {
                                        echo "不存在此用户";
                                    }
                                    elseif($_GET["message"]=="errorpass") {
                                        echo "密码错误";
				    }
				    elseif($_GET["message"]=='csrf'){
					echo "csrf token mismatch";
				    }
                                    else{
                                        echo "未知错误";
                                    }
                                    ?>
                                </div>
                            <?php } ?>
                            <button type="submit" class="btn btn-success" style="width:45%;float:left">Sign in!</button><a class="btn btn-danger" href="register.php" style="width:45%;float:right;">or Sign up!</a>
                            <button style="visibility: hidden"></button>

                        </form>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-6 col-sm-offset-3 social-login">
                    <h3>...or login with:</h3>
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
