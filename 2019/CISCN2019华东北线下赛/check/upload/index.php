<?php
error_reporting(0);
include "../config.php";
if($_SERVER["REMOTE_ADDR"]==="127.0.0.1"){
   echo "I think I can check my file contents in ch3ck.php";
}
if (isset($_POST["filename"])) {
    $filename = addslashes(htmlspecialchars_decode($_POST['filename']));
    $data = addslashes(htmlspecialchars_decode($_POST['data']));
    $fileext = ".txt";
    $filename = $filename . $fileext;

    $contents = "<?php highlight_string('{$data}'); ?>";
    file_put_contents($filename,$contents);
    echo "文件存储在: " . "./" . $filename;
    echo "这是你的文件的密钥:".md5($secret."filename".$filename);
}
?>

<html>
<head>
<title>upload</title>
<link href="../css/upload_style.css" rel="stylesheet" type="text/css" media="all"/>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" /> 
<!--Google Fonts-->
<link href='http://fonts.googleapis.com/css?family=Open+Sans:300italic,400italic,600italic,700italic,800italic,400,300,600,700,800' rel='stylesheet' type='text/css'>
<!--Google Fonts-->
</head>    
    <body>
    <div> 
    <h1>欢迎进入文件上传页面~</h1>
        <form method="post" action="index.php">
        <p>请输入文件名：
        <input type="text" name="filename"/><br></p>
        <h3>请输入要写入的文件内容：</h3>
        <textarea rows="10" type="text" name="data" placeholder="文件内容"></textarea><br>
        <input type="submit" />
      </form>
      </div>
    </body>
</html>
