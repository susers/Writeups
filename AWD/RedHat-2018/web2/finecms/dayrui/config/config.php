<?php

if (!defined('BASEPATH')) exit('No direct script access allowed');

$site = require WEBPATH.'config/system.php';
if (isset($site['SYS_GZIP']) && $site['SYS_GZIP'] && function_exists('ob_gzhandler')) {
    ob_start('ob_gzhandler');
}
date_default_timezone_set('Etc/GMT-8'); // 设置默认时区

$config['base_url']						= '';
$config['index_page']					= SELF;
$config['uri_protocol']					= 'AUTO';
$config['url_suffix']					= '';
$config['language']						= 'zh-cn';
$config['charset']						= 'UTF-8';
$config['enable_hooks']					= TRUE;
$config['composer_autoload']            = FALSE;
$config['subclass_prefix']				= 'M_';
$config['permitted_uri_chars']			= 'a-z 0-9~%.:_\-,';
$config['allow_get_array']				= TRUE;
$config['enable_query_strings']			= TRUE;
$config['controller_trigger']			= 'c';
$config['function_trigger']				= 'm';
$config['directory_trigger']			= 'd';

$config['log_threshold']				= 1;
$config['log_path']						= WEBPATH.'cache/errorlog/';
$config['log_file_extension']			= '';
$config['log_date_format']				= 'Y-m-d H:i:s';
$config['cache_path']					= WEBPATH.'cache/file/';

$config['encryption_key']				= $site['SYS_KEY'];
$config['sess_driver']					= 'files';
$config['sess_save_path']				= WEBPATH.'cache/session/';
$config['sess_valid_drivers']			= array();
$config['sess_cookie_name']				= md5(substr($site['SYS_KEY'],0, 5)).'_ci_session';
$config['sess_expiration']				= $site['SYS_ONLINE_TIME'] ? $site['SYS_ONLINE_TIME'] : 7200;
$config['sess_expire_on_close']			= FALSE;
$config['sess_encrypt_cookie']			= FALSE;
$config['sess_use_database']			= TRUE;
$config['sess_table_name']				= '';
$config['sess_match_ip']				= FALSE;
$config['sess_match_useragent']			= FALSE;
$config['sess_time_to_update']			= 7200;
$config['cookie_prefix']				= '';
$config['cookie_domain']				= '';
$config['cookie_path']					= '/';
$config['cookie_secure']				= FALSE;
$config['cookie_httponly']				= FALSE;


$config['global_xss_filtering']			= FALSE;
$config['csrf_protection']				= FALSE;
$config['csrf_token_name']				= 'csrf_test_name';
$config['csrf_cookie_name']				= 'csrf_cookie_name';
$config['csrf_expire']					= 7200;
$config['csrf_regenerate']				= TRUE;
$config['csrf_exclude_uris']			= array();


$config['compress_output']				= FALSE;
$config['minify_output']				= FALSE;
$config['time_reference']				= 'local';
$config['rewrite_short_tags']			= FALSE;
$config['proxy_ips']					= '';
$config['standardize_newlines']			= TRUE;
$config['is_post_xss_data']			= TRUE;
