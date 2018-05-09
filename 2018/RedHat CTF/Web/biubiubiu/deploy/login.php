<!DOCTYPE html>
<html lang="en" >

<head>
  <meta charset="UTF-8">
  <title>Welcome</title>
  <link href='https://fonts.googleapis.com/css?family=Lato:400,700' rel='stylesheet' type='text/css'>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/normalize/5.0.0/normalize.min.css">


      <link rel="stylesheet" href="css/style.css">


</head>

<body>
<?php
  session_start();
  #include_once("conn.php");

  if(isset($_POST["email"])&&isset($_POST["password"])){
    $_SESSION['login']=1;
    header("Location: index.php?page=send.php");
    exit();
  }
?>
  <section class="login-form-wrap">
  <h1>Login</h1>
  <form class="login-form"  method="POST" action="">
    <label>
      <input type="email" name="email" required placeholder="Email">
    </label>
    <label>
      <input type="password" name="password" required placeholder="Password">
    </label>
    <input type="submit" value="Login">
  </form>
</section>



</body>

</html>
