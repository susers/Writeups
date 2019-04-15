<?php
error_reporting(0);
require_once('db.inc.php');
if(!isset($_SESSION['login'])){
    header('Location:login.php');
}


if (isset($_POST['age']) && isset($_POST['school']) && isset($_POST['student_number'])){
    if(is_numeric($_POST['student_number'])) {
        $stmt = $mysqli->prepare("update users set age=?,school=?,student_number=? where id=?");
        $stmt->bind_param('ssss', $_POST['age'], $_POST['school'], $_POST['student_number'], $_SESSION['id']);
        $res = $stmt->execute();
        if (!$res) {
            header('Location:index.php');
            die("Fata error");
        }
        $stmt->close();
    }
    else{
        die('Student number must be number!!!');
    }
}
$s=$mysqli->prepare("select age,school,student_number from users where id=?");
$s->bind_param('i',$_SESSION['id']);
$res=$s->execute();
if(!$res){
    header('Location:index.php');
    die("Fata error");
}
$s->bind_result($age, $school, $student_number);
$s->fetch();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>基本信息修改</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<form action="#" method="post">
    <fieldset>
        <legend>基本信息修改</legend>
        <ul>
            <li>
                <label>年龄:</label>
                <input type="text" name="age" value="<?php echo $age;?>">
            </li>
            <li>
                <label>学院:</label>
                <input type="text" name="school" value="<?php echo $school;?>">
            </li>
            <li>
                <label>学号:</label>
                <input type="text" name="student_number" value="<?php echo $student_number;?>">
            </li>
            <input type="submit" value="修改">
        </ul>
    </fieldset>
    <a href="index.php">点击返回</a>
</body>
</html>
