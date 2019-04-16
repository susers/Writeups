<?php
/**
 * Created by PhpStorm.
 * User: y4ngyy
 * Date: 19-3-23
 * Time: 下午3:50
 */
session_start();
$conn = new mysqli("localhost", "root", "toor", "web2");
if (!isset($_POST['username'])||!isset($_POST['password'])) {
    echo "数据不全";
    exit();
}
$username = $_POST['username'];
$password = $_POST['password'];
//$sql = "select * from user;";
$sql = "select * from user where username='{$username}' and password='{$password}' limit 0,1;";
$result = $conn->query($sql);
$loginInfo = $result->fetch_array(MYSQLI_ASSOC);
if ($result === false || $loginInfo == NULL) {
    echo "登录失败";
    exit();
}
$_SESSION["username"] = $loginInfo['username'];
echo "登录成功";
