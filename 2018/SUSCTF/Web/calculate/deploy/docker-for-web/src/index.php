<?php
/*
 * File Name: sssion.php
 * Author: Image
 * mail: malingtao1019@163.com
 * Blog:http://blog.imagemlt.xyz
 * Created Time: 2018年03月26日 星期一 14时49分04秒
*/

function randrgb() 
{ 
  $str='0123456789ABCDEF'; 
    $estr='#'; 
    $len=strlen($str); 
    for($i=1;$i<=6;$i++) 
    { 
        $num=rand(0,$len-1);   
        $estr=$estr.$str[$num];  
    } 
    return $estr; 
} 
session_start();
if(!isset($_SESSION['count']))$_SESSION['count']=0;
if(isset($_SESSION['ans']) && isset($_POST['ans'])){
	if($_SESSION['ans']!=intval($_POST['ans'])){
		session_destroy();
		die("calculate error!");
	}
	else{
		if(intval(time())-$_SESSION['time']<1){
			session_destroy();
			die("too fast!!!");
			
		}
		if(intval(time())-$_SESSION['time']>2){
			session_destroy();
			die("timeout");
		}
		$_SESSION['count']++;
	}
}
if($_SESSION['count']>=20){
	session_destroy();
	die('Susctf{gr3At_cAcu1a7or}');
}
$num1=rand(0,1000);
$num2=rand(0,1000);
$mark=rand(0,3);
$num3=0;
switch($mark){
case 0:
	$_SESSION['ans']=$num1+$num2;
	break;
case 1:
	$_SESSION['ans']=$num1-$num2;
	break;
case 2:
	$_SESSION['ans']=$num1*$num2;
	break;
case 3:
	$_SESSION['ans']=$num1+$num2+$num1*$num2;
	break;
}
$_SESSION['time']=intval(time());
?>
<h1>Answer my questions</h1>

<p>Answer the following methematics questions for 20 times;you have 3 seconds to solve each question;</p>
<p>In order to protect the server you can't ans one question in less than 1 second</p>
<p> You Have answered <?php echo $_SESSION['count'];?>　questions;</p>

<form action="" method="post">
<?php 
$sentence="";
switch($mark){
case 0:
	$sentence="$num1+$num2=";
	break;
case 1:
	$sentence="$num1-$num2=";
	break;
case 2:
	$sentence="$num1*$num2=";
	break;
case 3:
	$sentence="$num1+$num2+$num1*$num2=";
	break;
}
for($i=0;$i<strlen($sentence);$i++){
	echo "<div style=\"display:inline;color:".randrgb()."\">".$sentence[$i]."</div>";
}
?>

<input type="text" name="ans">
<input type="submit" value="send!">
</form>












