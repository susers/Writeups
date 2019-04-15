<?php
/**
 * Created by PhpStorm.
 * User: Machenike
 * Date: 19/3/21
 * Time: 21:28
 */

$hostname = 'localhost';
$dbuser = 'root';
$dbpass = 'toor';
$database = 'ctf';
$mysqli = new mysqli($hostname, $dbuser, $dbpass, $database);

session_start();