<?php
require_once("Core/user.class.php");
require_once("Core/db.class.php");
require_once("config.php");
require_once("function.php");
parseurl();

$db=new Db($dbhost,$dbuser,$dbpass,$dbname,$feilds);
$user=new User($db);

$controller="Controller/"."index.controller.php";
if(file_exists($controller)){
    include($controller);
	$className="Action";
	$controler=new $className($user);
	$controler->run();
}
?>
