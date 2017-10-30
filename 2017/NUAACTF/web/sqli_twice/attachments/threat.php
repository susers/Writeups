<?php

if (!defined('CTF')) {
    die('hacking attempt.');
}

// using prepare to prevent sql injection
$stmt = $db->prepare('DELETE FROM `users` WHERE `username` = ?');
$stmt->bind_param('s', $_SESSION['user']);
$stmt->execute();
