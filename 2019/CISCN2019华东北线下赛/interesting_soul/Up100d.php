<!DOCTYPE html>
<html>
<head>
  <title>0000</title>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
</head>
<body>
<?php
error_reporting(0);
if (isset($_POST['Upload'])){
  if (($_FILES["Up10defile"]["type"]=="image/gif")&&(substr($_FILES["Up10defile"]["name"], strrpos($_FILES["Up10defile"]["name"], '.')+1))== 'gif'&&$_FILES["file"]["size"]<1024000) {
      echo "Upload: " . $_FILES["Up10defile"]["name"]."<br>";
      echo "Type: " . $_FILES["Up10defile"]["type"]."<br>";

      if (file_exists("upload_file/" . $_FILES["Up10defile"]["name"]))
      {
        echo $_FILES["Up10defile"]["name"] . " already exists. ";
      }
      else
      {
        move_uploaded_file($_FILES["Up10defile"]["tmp_name"],
        "upload_file/" .$_FILES["Up10defile"]["name"].".gif");
        echo "Stored in: " . "upload_file/" . $_FILES["Up10defile"]["name"].".gif";
      }
    }
    else
    {
      echo "Invalid file,you can only upload gif";
    }
 }
?>
<!--
你竟然找到了我，你知道下一个flag是真的吗？？
4C4A5757593432324B593457555952534755594747334B4750465356474E4C584D4645454350493D
-->
</body>
</html>
