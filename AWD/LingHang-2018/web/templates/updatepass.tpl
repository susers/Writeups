<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<link rel="icon" href="favicon.ico">
		<title>学生会-简历系统</title>
		<!-- Bootstrap core CSS -->

		<link href="./public/css/bootstrap.min.css" rel="stylesheet">
		<script src="./public/js/jquery.min.js"></script>
        <style>
body {
  padding-top: 40px;
  padding-bottom: 40px;
  background-color: #eee;
}

.form-signin {
  max-width: 330px;
  padding: 15px;
  margin: 0 auto;
}
.form-signin .form-signin-heading,
.form-signin .checkbox {
  margin-bottom: 10px;
}
.form-signin .checkbox {
  font-weight: normal;
}
.form-signin .form-control {
  position: relative;
  height: auto;
  -webkit-box-sizing: border-box;
     -moz-box-sizing: border-box;
          box-sizing: border-box;
  padding: 10px;
  font-size: 16px;
}
.form-signin .form-control:focus {
  z-index: 2;
}
.form-signin input[type="email"] {
  margin-bottom: -1px;
  border-bottom-right-radius: 0;
  border-bottom-left-radius: 0;
}
.form-signin input[type="password"] {
  margin-bottom: 10px;
  border-top-left-radius: 0;
  border-top-right-radius: 0;
}
</style>
	</head>
	<body>
		<form   class="form-signin" id="test" method="post"  action="index.php?c=User&a=updatepass">		 
			<h2 class="form-signin-heading">修改密码</h2>
			<label for="inputPassword" class="sr-only">新密码</label>
			<input type="password"  name="password" id="password" class="form-control" placeholder="密码" required>
			<input  class="btn btn btn-warning btn-block"  type="submit" value="修改"> </input>
		  <a  class="btn btn btn-warning btn-block" href="./index.php">返回</a>
    </form>
	</body>
</html>