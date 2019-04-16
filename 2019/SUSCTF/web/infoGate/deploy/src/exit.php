<?php
/**
 * Created by PhpStorm.
 * User: y4ngyy
 * Date: 19-3-26
 * Time: 下午3:33
 */
session_start();
if (isset($_SESSION['username'])) {
    unset($_SESSION['username']);
}
header("Location:/login.php");