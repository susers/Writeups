<?php

session_start();

require 'user.php';
require_once 'db.php';

$db = new DB();
$username = $_POST['username'];
$passwd = $_POST['passwd'];

if ($res = $db->login($username, $passwd))
{
    $_SESSION['username'] = $res['username'];
    $_SESSION['no'] = $res['no'];
    $_SESSION['data'] = $res['data'];

    $message = "<script>alert('Login success'); location.href='/';</script>";
    echo $message;
}

else
{
    $message = "<script>alert('Login failed'); history.back();</script>";
    echo $message;
}
