<?php
/**
 * Created by PhpStorm.
 * User: Machenike
 * Date: 19/3/22
 * Time: 18:42
 */
error_reporting(0);
require_once ("db.inc.php");
require_once ("config.php");
if(!isset($_SESSION['login'])){
    header('Location:login.php');
}
unserialize(data_process($_GET['debug']));
if (isset($_GET["build"])){
    $stmt = $mysqli->prepare("select username,html from users where id=?");
    $stmt->bind_param('i', $_SESSION['id']);
    $id =$_SESSION['id'];
    $bool = $stmt->execute();
    if($bool){
        $stmt ->bind_result($uname,$file);
        $stmt ->fetch();
        if($file!=NULL){
            unserialize(data_process($file));
        }
        else {
            $html = new html($uname);
            $path = $html->read();
            $myhtml = fopen($path, "w");
            $content = file_get_contents("./html/template.html");
            fwrite($myhtml, $content);
            fclose($myhtml);
            $html->run();
            $stmt->close();
            $data = "0x".bin2hex(serialize($html));
            $mysqli->query("update users set html='$data' where id=$id");
        }
    }
    else{
        die('Fata error');
    }

}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>生成HTML文件</title>
    </table>
    <link rel="stylesheet" href="http://libs.baidu.com/bootstrap/3.0.3/css/bootstrap.min.css" />
</head>
<body>
<form action="#" method="get" >
    <button type="submit" name="build">点此预览你的HTML个人页面</button>
</form>
<a href="index.php">点击返回</a>
<!-- ?debug -->
</body>
</html>
