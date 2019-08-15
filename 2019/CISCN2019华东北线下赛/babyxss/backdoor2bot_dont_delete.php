<?php
/**
 * Created by PhpStorm.
 * User: 5am3
 * Date: 2019/5/5
 * Time: 6:50 PM
 */

ini_set("session.cookie_httponly", 1);
session_start();

$_SESSION['login'] = 1;
$_SESSION['uid'] = 1;
$_SESSION['username'] = '5am3';