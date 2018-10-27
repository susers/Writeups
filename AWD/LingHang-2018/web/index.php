<?php
include "./common/function.php";
include "./include/config.php";
include "./lib/base.php";
include "./include/log1.php";


ini_set("display_errors","On");

$c=isset($_GET['c'])?$_GET['c']:'User';
$a=isset($_GET['a'])?$_GET['a']:'Index';

$obj=run_c($c);
run_a($obj,$a);
?>
