<?php

if (!isset($_GET['file'])) {
    header('Location: ?file=flag');
    exit();
}

$file = trim($_GET['file']);

if (preg_match('/\s/', $file)) {
    die('Trying to use space huh?');
}

if (preg_match('/\.\./', $file)) {
    die('Trying to include files from parent directory huh?');
}

if (preg_match('/^\//', $file)) {
    die('Trying to include files from root directory huh?');
}

require_once $file . '.php';
