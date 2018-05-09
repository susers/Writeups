<?php

$db_host = 'mysql';
$db_name = 'user_admin';
$db_user = 'Dog';
$db_pwd = '';

$conn = mysqli_connect($db_host, $db_user, $db_pwd, $db_name);

if(!$conn){
    die(mysqli_connect_error());
}
