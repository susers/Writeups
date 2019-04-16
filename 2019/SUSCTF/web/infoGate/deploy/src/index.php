<?php
/**
 * Created by PhpStorm.
 * User: y4ngyy
 * Date: 19-3-23
 * Time: 下午2:36
 */
session_start();
if ($_SESSION["username"] == null) {
    header("Location:/login.php");
    exit();
}?>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="https://cdn.bootcss.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">

    <title>文件编辑</title>
  </head>
  <body>
  <nav class="navbar navbar-expand-lg navbar-dark bg-dark justify-content-between">
      <a class="navbar-brand" href="#">信息门户</a>
      <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
      </button>

      <div class="collapse navbar-collapse" id="navbarSupportedContent">
          <ul class="navbar-nav mr-auto">
              <li class="nav-item active">
                  <a class="nav-link" href="#">Home <span class="sr-only">(current)</span></a>
              </li>
              <?php
              if ($_SESSION["username"] == 'admin')
                echo "<li class=\"nav-item\"><a class=\"nav-link\" href=\"/edit.php\">Upload</a></li>";
              ?>
              <li class="nav-item">
                  <a class="nav-link" href="/exit.php">退出</a>
              </li>
          </ul>
      </div>
  </nav>
  <?php
  echo "<h1 class='m-5'>Hello,".$_SESSION["username"]."</h1>";
  ?>
  <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
  <script src="https://cdn.bootcss.com/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
  <script src="https://cdn.bootcss.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
  </body>
</html>
