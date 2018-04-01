<?php
/*
 * File Name: deal.php
 * Author: Image
 * mail: malingtao1019@163.com
 * Blog:http://blog.imagemlt.xyz
 * Created Time: 2018年03月19日 星期一 19时19分18秒
 */
$message=isset($_POST['content'])?$_POST['content']:'';
$f=file_get_contents("./template.html");
$message=preg_replace('/script/i','',$message);
$data=str_replace('{{content}}',$message,$f);
$str="QWERTYUIOPASDFGHJKLZXCVBNM1234567890qwertyuiopasdfghjklzxcvbnm";
str_shuffle($str);
$filename=md5($str.date("Y-m-d h:i:sa"));
$m=fopen('tmp1/'.$filename.'.html','w');
fwrite($m,$data);
fclose($m);
$command="DISPLAY=:9 phantomjs /var/scripts/shotpic.js   /var/www/html/tmp1/$filename.html /var/www/html/pics/$filename.png 2>&1";
shell_exec($command);

header('Content-Type:application/json');
echo json_encode(array("result"=>"success","ans"=>"/pics/".$filename.".png"));
