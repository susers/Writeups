<?php
/**
 * https://gitee.com/greenlaw
 **/


define('IS_ADMIN', TRUE); // 项目标识
define('SELF', pathinfo(__FILE__, PATHINFO_BASENAME)); // 该文件的名称
$_GET['d'] = 'admin'; // 将项目标识作为directory
require('index.php'); // 引入主文件