<?php
/**
 * Created by PhpStorm.
 * User: Machenike
 * Date: 19/3/22
 * Time: 18:41
 */
error_reporting(0);
require_once('db.inc.php');
require_once ('config.php');
if(!isset($_SESSION['login'])){
    header('Location:login.php');
}

?>



<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>登陆信息查看</title>
    </table>
    <link rel="stylesheet" href="http://libs.baidu.com/bootstrap/3.0.3/css/bootstrap.min.css" />
</head>
<body>



<table border="1" align="mid">
    <tr>
        <th>登陆学号</th>
        <th>登陆IP地址</th>
        <th>登陆时间</th>
    </tr>
<?php
    require_once('db.inc.php');
    $stmt = $mysqli->prepare("select student_number,ip,time from log where id=?");
    $stmt->bind_param('i', $_SESSION['id']);
    $bool = $stmt->execute();
    $stmt->store_result();
    $row = $stmt->num_rows;
    for ($i=0; $i<$row; $i++){
        $stmt->bind_result($student_num, $ip, $time);
        $stmt->fetch();
        echo "</th>\r\n<th>" . $student_num . "</th>\r\n<th>" . $ip . "</th>\r\n<th>" . $time . "</th>\r\n</tr>";
    }


?>
</table>
<a href="index.php">点击返回</a>
<script src="http://code.jquery.com/jquery-latest.js" />
<script src="http://libs.baidu.com/bootstrap/3.0.3/js/bootstrap.min.js" />
</body>
</html>
