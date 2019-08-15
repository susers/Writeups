<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>check</title>
</head>
<body>
    <form action="ch3ck.php">
        文件名
        <input type="text" name="filename">
        <br />
        key
        <input type="text" name="key" />
        <input type="submit"/>
    </form>
</body>
</html>


<?php
    error_reporting(0);
    include "config.php";
    if($_SERVER["REMOTE_ADDR"]==="127.0.0.1"){
       highlight_file(__FILE__);
    }
    if(isset($_GET["filename"])&&isset($_GET["key"])){
        $file_str='';
        foreach ($_GET as $key => $value) {
            if($key!="key"){
                $file_str = $file_str.urldecode($key.$value);
            }
        }
        if ($_GET["key"] === md5($secret.$file_str)) {
            include("upload/".$_GET["filename"]);
        }
        else{
            echo "文件名或者key出错！！！";
        }
    }
    else{
        echo "请输入要查看的文件名和key";
    }
?>