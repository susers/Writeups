<?php

error_reporting(E_ALL);
ini_set("display_errors", 1);

session_start();
require_once 'db.php';
require_once 'user.php';

$db = new DB();

$username = $_POST['username'];
$passwd = $_POST['passwd'];
$age = $_POST['age'];
$blog = $_POST['blog'];

$user = new UserInfo($username, $age, $blog);

if (!$user->isValidBlog())
{
    $message = "<script>alert('Blog is not valid.'); history.back();</script>";
    die($message);
}

if (!$db->isValidUsername($username))
{
    $message = "<script>alert('Username already exists.'); history.back();</script>";
    die($message);
}

$db->insertUser($username, $passwd, $user);

if ($res = $db->login($username, $passwd))
{
    $_SESSION['username'] = $username;
    $message = "<script>alert('Join success'); location.href='/';</script>";
    echo $message;
}

