<?php

$file = $_GET['file'];

echo file_get_contents("uploadDir/$file");
exit;
