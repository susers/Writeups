<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>upload_2_shell</title>

    <!-- Bootstrap core CSS -->
    <link href="/static/css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom styles for this template -->
    <link href="/static/css/custom.css" rel="stylesheet">
  </head>
<body>
<?php
define('MB', 1048576);
$userdir = "images/".md5($_SERVER["REMOTE_ADDR"]);
if (!file_exists($userdir)) {
    mkdir($userdir);
}
if (isset($_POST["upload"])){
    $tmp_name = $_FILES["fileUpload"]["tmp_name"];
    $name = $_FILES["fileUpload"]["name"];
    if(!$tmp_name){
        die("filesize too big!");
    }
    if(!$name){
        die("filename cannot be empty!");
    }
    $extension = substr($name,strrpos($name,".")+1);
    if(preg_match("/ph/i",$extension)){
        die("illegal suffix!");
    }
    if (mb_strpos(file_get_contents($tmp_name), "<?") !== FALSE) {
        die("&lt;? in contents!");
    }
    $image_type = exif_imagetype($tmp_name);
    if(!$image_type){
        die("exif_imagetype:not image!");
    }
    $upload_file_path = $userdir."/".$name;
    move_uploaded_file($tmp_name, $upload_file_path);
}
?>
<div class="text-center">
    <h3 class="h3 mb-3 font-weight-normal">上传图片</h3>
    <form action="index.php" method="post" enctype="multipart/form-data">
    <input class="form-control" type="file" name="fileUpload" style="margin:20px 0px;" />
    <button class="btn btn-lg btn-primary btn-block" name="upload" type="submit">上传</button>
    </form>
    <?php
    if(isset($upload_file_path)){
        echo "<div class=\"alert alert-success\">$upload_file_path</div>";
    }
    ?>
</div>


<script src="/static/js/jquery-3.3.1.min.js" ></script>
    <script src="/static/js/bootstrap.min.js"></script>
  </body>
</html>