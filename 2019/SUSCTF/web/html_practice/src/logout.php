<?php
/**
 * Created by PhpStorm.
 * User: Machenike
 * Date: 19/3/22
 * Time: 19:54
 */

session_start();
session_destroy();
header('Location:index.php');