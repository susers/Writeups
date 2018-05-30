<?php

if (!defined('BASEPATH')) exit('No direct script access allowed');

$autoload['libraries']		= array('dcache', 'encrypt', 'duri');
$autoload['language']		= array();
$autoload['drivers']		= array('cache');
$autoload['config']			= array();
$autoload['helper']			= array('durl', 'function', 'url', 'language', 'cookie', 'directory', 'my', 'system');
$autoload['model']			= array();

$autoload['packages'][]		= FCPATH.'dayrui/';