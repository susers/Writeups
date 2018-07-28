<?php
include "config.php";
$number1 = rand(1,100000000000000);
$number2 = rand(1,100000000000);
$number3 = rand(1,100000000);
$url = urldecode($_SERVER['REQUEST_URI']);
$url = parse_url($url, PHP_URL_QUERY);
if (preg_match("/_/i", $url)) 
{
    die("...");
}
if (preg_match("/0/i", $url)) 
{
    die("...");
}
if (preg_match("/\w+/i", $url)) 
{
    die("...");
}    
if(isset($_GET['_']) && !empty($_GET['_']))
{
    $control = $_GET['_'];        
    if(!in_array($control, array(0,$number1)))
    {
        die("fail1");
    }
    if(!in_array($control, array(0,$number2)))
    {
        die("fail2");
    }
    if(!in_array($control, array(0,$number3)))
    {
        die("fail3");
    }
    echo $flag;
}
show_source(__FILE__);




?> 
