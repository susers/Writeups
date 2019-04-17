<?php
/**
 * Created by PhpStorm.
 * User: Machenike
 * Date: 19/3/27
 * Time: 20:52
 */
error_reporting(0);
$flag = "SUSCTF{python_1s_th3_be3t_l4ngu4ge}";
if(!session_id())
    session_start();

if(!isset($_SESSION['count']))
    $_SESSION['count']=0;

if(isset($_SESSION['answer']) && isset($_POST['answer'])){
    if(($_SESSION['answer'])!==$_POST['answer']){
        session_destroy();
        die('答案错误');
    }
    else{
        if(intval(time())-$_SESSION['time']<1){
            session_destroy();
            die('比香港记者还快！');
        }
        if(intval(time())-$_SESSION['time']>2){
            session_destroy();
            die('太慢了');
        }
        $_SESSION['count']++;
    }
}
if($_SESSION['count']>=20){
    session_destroy();
    echo $flag;
    die();
}
$num1=rand(0,1000);
$num2=rand(0,1000);
$num3=rand(0,1000);
$num4=rand(0,1000);
$mark=rand(0,3);
$ans = 0;
switch($mark){
    case 0:
        $ans=$num1+$num2-$num3*$num4;
        break;
    case 1:
        $ans=$num1*$num2+$num3-$num2;
        break;
    case 2:
        $ans=$num1*$num4*$num3-$num2;
        break;
    case 3:
        $ans=$num1+$num4+$num3*$num2;
        break;
}
$out = rand($ans-1, $ans+1);
if ($ans === $out){
    $_SESSION['answer']='true';
}
else{
    $_SESSION['answer']='false';
}
$_SESSION['time']=intval(time());
?>
<h1>游戏规则</h1>

<p>判断以下式子答案的正误</p>
<p>在两秒内提交你的答案，式子正确提交true，错误提交false,答对20次可以获得flag</p>
<p>由于某位长者很讨厌速度快的记者，所以你不能在1s内提交</p>
<p> 你已经回答了 <?php echo $_SESSION['count'];?>个问题</p>

<form action="" method="post">
<?php
$sentence="";
switch($mark) {
    case 0:
        $sentence = "$num1 + $num2 - $num3 * $num4";
        break;
    case 1:
        $sentence = "$num1 * $num2 + $num3 - $num2";
        break;
    case 2:
        $sentence = "$num1 * $num4 * $num3 - $num2";
        break;
    case 3:
        $sentence = "$num1 + $num4 + $num3 * $num2";
        break;
}
echo "<div".">".$sentence."=".strval($out)."</div>";
?>
    <input type="text" name="answer">
    <input type="submit" value="提交">
</form>
