<?php
/**
 * Created by PhpStorm.
 * User: image
 * Date: 18-3-17
 * Time: 下午1:08
 */
require_once("db.inc.php");
if(!isset($_SESSION['login'])){
    header('Location:login.php');
    die();
}
if($_POST['token']!=$_SESSION['token']){
	header('Location:index.php?message=csrf');
	die("csrf token mismatch");
}
$stmt=$mysqli->prepare("select * from users where id=?");
$stmt->bind_param('i',$_SESSION['id']);
$res=$stmt->execute();
if(!$res){
    header('Location:index.php?message=error');
    die("Fata error");
}
$user=Array();
while($row=$stmt->fetch()){
    $user=$row;
}
$stmt->close();
if(!get_magic_quotes_gpc())
foreach($_POST as $key=>$value){
    $_POST[$key]=addslashes($value);
}
$query=$mysqli->query("update users set age=$_POST[age],nickname='$_POST[nickname]',description='$_POST[description]' where id=$_SESSION[id]");

if(!$query){
    $mysqli->close();
    header('Location:index.php?message=error');
    die('Update error');
}
else{
    header('Location:index.php');
    $mysqli->close();
    die('Update message success');
}
?>
