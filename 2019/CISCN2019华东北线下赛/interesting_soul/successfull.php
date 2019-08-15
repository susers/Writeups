<!DOCTYPE html>
<html>
<head>
  <title>BobAdmin</title>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
</head>
<body>
<?php
error_reporting(0);
session_start();
if(isset($_COOKIE['token']) && isset($_COOKIE['cookie']) &&($_COOKIE['token']==md5(md5("admin".$_COOKIE['cookie'])))){
    echo "<hr/>"."<h1 style='text-align:center'>登陆成功 ~v~ </h1>";
    echo "你好! admin ,欢迎来到个人中心!<br><br>你知道Bob接下来会做什么吗！<br><br><br>.";
    echo '<form action="./Up100d.php" method="post" enctype="multipart/form-data">
    <input type="file" name="Up10defile" />
    <input type="submit" name="Upload" />
</form>';
  }
  else {
    header("location:index.html");
  }   
?>
</body>
</html>
