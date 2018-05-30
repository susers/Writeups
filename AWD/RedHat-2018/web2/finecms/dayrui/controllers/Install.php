<?php

if (!defined('BASEPATH')) exit('No direct script access allowed');

/* v5.0  */

class Install extends CI_Controller {

    /**
     * 构造函数
     */
    public function __construct() {
        parent::__construct();
        is_file(WEBPATH.'cache/install.lock') && exit('安装程序已经锁定，如果重新安装请删除cache/install.lock文件');
        version_compare(PHP_VERSION, '5.3.0') < 0 && exit('当前环境PHP'.PHP_VERSION.'，其版本太低，必须是在PHP5.3以上');
        $this->load->library('template');
        define('SITE_PATH', str_replace(array('\\', '//'), '/', str_replace(array('/'.APP_DIR, 'member'), '', dirname($_SERVER['SCRIPT_NAME'])).'/'));
        define('SITE_THEME', 'default');
        define('SITE_TEMPLATE', 'default');
        define('SYS_UPLOAD_DIR', 'uploadfile');
        $config = require WEBPATH.'config/version.php'; // 加载系统版本更新文件
        foreach ($config as $var => $value) {
            !defined($var) && define($var, $value); // 将配置文件数组转换为常量
        }
        $this->load->library('dcache');
        $this->dcache->set('install', TRUE);
        if (strlen(DR_URI) > 1 && strpos(DR_URI, '/') !== FALSE) {
            header('Content-Type: text/html; charset=utf8');
            show_error('禁止子目录安装('.DR_URI.')，请放置在网站根目录安装', 404);
        }
    }

    /**
     * 安装程序
     */
    public function index() {

        $step = max(1, (int)$this->input->get('step'));
        switch ($step) {

            case 1:
                break;

            case 2:

                $check_pass = true;

                $writeAble = $this->_checkFileRight();
                $lowestEnvironment = $this->_getLowestEnvironment();
                $currentEnvironment = $this->_getCurrentEnvironment();
                $recommendEnvironment = $this->_getRecommendEnvironment();

                foreach ($currentEnvironment as $key => $value) {
                    if (false !== strpos($key, '_ischeck') && false === $value) {
                        $check_pass = false;
                    }
                }
                foreach ($writeAble as $value) {
                    if (false === $value) {
                        $check_pass = false;
                    }
                }

                $this->template->assign(array(
                    'writeAble' => $writeAble,
                    'check_pass' => $check_pass,
                    'lowestEnvironment' => $lowestEnvironment,
                    'currentEnvironment' => $currentEnvironment,
                    'recommendEnvironment' => $recommendEnvironment,
                ));
                break;
            case 3:

                if ($_POST) {
                    $data = $this->input->post('data');
                    // 数据库支持判断
                    $mysqli = function_exists('mysqli_init') ? mysqli_init() : 0;
                    if (version_compare(PHP_VERSION, '5.5.0') >= 0 && !$mysqli) {
                        exit(dr_json(0, '您的PHP环境必须启用Mysqli扩展'));
                    }
                    // 参数判断
                    if (!$data['dbname']) {
                        exit(dr_json(0, '数据库名称不能为空'));
                    }
                    if (is_numeric($data['dbname'])) {
                        exit(dr_json(0, '数据库名称不能是数字'));
                    }
                    if (strpos($data['dbname'], '.') !== false) {
                        exit(dr_json(0, '数据库名称不能存在.号'));
                    }

                    if ($mysqli) {
                        if (!@mysqli_real_connect($mysqli, $data['dbhost'], $data['dbuser'], $data['dbpw'])) {
                            exit(dr_json(0, '无法连接到数据库服务器（'.$data['dbhost'].'），请检查用户名（'.$data['dbuser'].'）和密码（'.$data['dbpw'].'）是否正确'));
                        }
                        if (!@mysqli_select_db($mysqli, $data['dbname'])) {
                            if (!@mysqli_query($mysqli, 'CREATE DATABASE '.$data['dbname'])) {
                                exit(dr_json(0, '指定的数据库（'.$data['dbname'].'）不存在，系统尝试创建失败，请通过其他方式建立数据库'));
                            }
                        }
                        // utf8方式打开数据库
                        mysqli_query($mysqli, 'SET NAMES utf8');
                    } else {
                        if (!@mysql_connect($data['dbhost'], $data['dbuser'], $data['dbpw'])) {
                            exit(dr_json(0, mysql_error().'<br>无法连接到数据库服务器（'.$data['dbhost'].'），请检查用户名（'.$data['dbuser'].'）和密码（'.$data['dbpw'].'）是否正确'));
                        }
                        if (!@mysql_select_db($data['dbname'])) {
                            if (!@mysql_query('CREATE DATABASE '.$data['dbname'])) {
                                exit(dr_json(0, mysql_error().'<br>指定的数据库（'.$data['dbname'].'）不存在，系统尝试创建失败，请通过其他方式建立数据库'));
                            }
                        }
                        // utf8方式打开数据库
                        mysql_query('SET NAMES utf8');
                    }

                    // 格式化端口
                    list($data['dbhost'], $data['dbport']) = explode(':', $data['dbhost']);
                    $data['dbport'] = $data['dbport'] ? (int)$data['dbport'] : 3306;
                    if(!is_string($data['dbprefix'])){
                        die("Not Allowed");
                    }
                    if(strlen($data['prefix'] > 6)){
                        die("Too long");
                    }
                    $data['dbprefix'] = $data['dbprefix'] ? $data['dbprefix'] : 'dr_';
                    // 配置文件
                    $config = "<?php".PHP_EOL.PHP_EOL;
                    $config.= "if (!defined('BASEPATH')) exit('No direct script access allowed');".PHP_EOL.PHP_EOL;
                    $config.= "\$active_group	= 'default';".PHP_EOL;
                    $config.= "\$query_builder	= TRUE;".PHP_EOL.PHP_EOL;
                    $config.= "\$db['default']	= array(".PHP_EOL;
                    $config.= "	'dsn'		=> '',".PHP_EOL;
                    $config.= "	'hostname'	=> '{$data['dbhost']}',".PHP_EOL;
                    $config.= "	'username'	=> '{$data['dbuser']}',".PHP_EOL;
                    $config.= "	'password'	=> '{$data['dbpw']}',".PHP_EOL;
                    $config.= "	'port'		=> '{$data['dbport']}',".PHP_EOL;
                    $config.= "	'database'	=> '{$data['dbname']}',".PHP_EOL;
                    $config.= "	'dbdriver'	=> '".($mysqli ? 'mysqli' : 'mysql')."',".PHP_EOL;
                    $config.= "	'dbprefix'	=> '{$data['dbprefix']}',".PHP_EOL;
                    $config.= "	'pconnect'	=> FALSE,".PHP_EOL;
                    $config.= "	'db_debug'	=> true,".PHP_EOL;
                    $config.= "	'cache_on'	=> FALSE,".PHP_EOL;
                    $config.= "	'cachedir'	=> 'cache/sql/',".PHP_EOL;
                    $config.= "	'char_set'	=> 'utf8',".PHP_EOL;
                    $config.= "	'dbcollat'	=> 'utf8_general_ci',".PHP_EOL;
                    $config.= "	'swap_pre'	=> '',".PHP_EOL;
                    $config.= "	'autoinit'	=> FALSE,".PHP_EOL;
                    $config.= "	'encrypt'	=> FALSE,".PHP_EOL;
                    $config.= "	'compress'	=> FALSE,".PHP_EOL;
                    $config.= "	'stricton'	=> FALSE,".PHP_EOL;
                    $config.= "	'failover'	=> array(),".PHP_EOL;
                    $config.= ");".PHP_EOL;
                    // 保存配置文件
                    if (!file_put_contents(WEBPATH.'config/database.php', $config)) {
                        exit(dr_json(0, '数据库配置文件保存失败，请检查文件config/database.php权限！'));
                    }
                    // key
                    $system = file_get_contents(WEBPATH.'config/system.php');
                    if (!file_put_contents(WEBPATH.'config/system.php', str_replace('finecms5key', 'finecms'.md5(time()), $system))) {
                        exit(dr_json(0, '配置文件保存失败，请检查文件config/system.php权限！'));
                    }

                    // 加载数据库
                    $this->load->database();
                    $this->db->db_debug = false;


                    // 导入表结构
                    $this->_query(str_replace(
                        array('{dbprefix}', '{site_url}'),
                        array($this->db->dbprefix, strtolower($_SERVER['HTTP_HOST'])),
                        file_get_contents(WEBPATH.'cache/install/install.sql')
                    ));

                    if ($data['demo']) {
                        // 导入默认数据
                        $this->_query(str_replace(
                            array('{dbprefix}', '{site_url}'),
                            array($this->db->dbprefix, strtolower($_SERVER['HTTP_HOST'])),
                            file_get_contents(WEBPATH.'cache/install/default.sql')
                        ));
                    }

                    exit(dr_json(1, dr_url('install/index', array('step' => $step + 1))));
                }
                break;

            case 4:
                $log = array();
                $sql = file_get_contents(WEBPATH.'cache/install/install.sql');
                preg_match_all('/`\{dbprefix\}(.+)`/U', $sql, $match);
                if ($match) {
                    $log = array_unique($match[1]);
                }
                $this->template->assign(array(
                    'log' => implode('<finecms>', $log),
                ));
                break;

            case 5:
                file_put_contents(WEBPATH.'cache/install.lock', time());
                file_put_contents(WEBPATH.'cache/install.new', time());
                break;
        }

        $this->template->assign(array(
            'step' => $step,
        ));
        $this->template->display('install_'.$step.'.html', 'admin');
    }

    // 执行sql
    private function _query($sql) {

        if (!$sql) {
            return NULL;
        }

        $sql_data = explode(';SQL_FINECMS_EOL', trim(str_replace(array(PHP_EOL, chr(13), chr(10)), 'SQL_FINECMS_EOL', $sql)));

        foreach($sql_data as $query){
            if (!$query) {
                continue;
            }
            $ret = '';
            $queries = explode('SQL_FINECMS_EOL', trim($query));
            foreach($queries as $query) {
                $ret.= $query[0] == '#' || $query[0].$query[1] == '--' ? '' : $query;
            }
            if (!$ret) {
                continue;
            }
            $this->db->query($ret);
        }
    }

    /**
     * 获得当前的环境信息
     *
     * @return array
     */
    private function _getCurrentEnvironment() {
        $lowestEnvironment = $this->_getLowestEnvironment();
        $space = floor(@disk_free_space(WEBPATH) / (1024 * 1024));
        $space = $space ? $space . 'M': 'unknow';
        $currentUpload = ini_get('file_uploads') ? ini_get('upload_max_filesize') : 'unknow';
        $upload_ischeck = intval($currentUpload) >= intval($lowestEnvironment['upload']) ? true : false;
        $space_ischeck = intval($space) >= intval($lowestEnvironment['space']) ? true : false;
        $version_ischeck = version_compare(phpversion(), $lowestEnvironment['version']) < 0 ? false : true;
        $pdo_mysql_ischeck = extension_loaded('pdo_mysql');
        if (function_exists('mysql_get_client_info')) {
            $mysql = mysql_get_client_info();
            $mysql_ischeck = true;
        } elseif (function_exists('mysqli_get_client_info')) {
            $mysql = mysqli_get_client_info();
            $mysql_ischeck = true;
        } elseif ($pdo_mysql_ischeck) {
            $mysql_ischeck = true;
            $mysql = 'unknow';
        } else {
            $mysql_ischeck = false;
            $mysql = 'unknow';
        }
        if (function_exists('gd_info')) {
            $gdinfo = gd_info();
            $gd = $gdinfo['GD Version'];
            $gd_ischeck = version_compare($lowestEnvironment['gd'], $gd) < 0 ? false : true;
        } else {
            $gd_ischeck = false;
            $gd = 'unknow';
        }
        return array(
            'gd' => $gd,
            'os' => PHP_OS,
            'json' => function_exists('json_encode'),
            'space' => $space,
            'mysql' => $mysql,
            'upload' => $currentUpload,
            'version' => phpversion(),
            'pdo_mysql' => $pdo_mysql_ischeck,
            'gd_ischeck' => $gd_ischeck,
            'os_ischeck' => true,
            'space_ischeck' => $space_ischeck,
            'mysql_ischeck' => $mysql_ischeck,
            'version_ischeck' => $version_ischeck,
            'upload_ischeck' => $upload_ischeck,
            'pdo_mysql_ischeck' => $pdo_mysql_ischeck,
        );
    }

    /**
     * 获取推荐的环境配置信息
     *
     * @return array
     */
    private function _getRecommendEnvironment() {
        return array(
            'os' => 'Linux',
            'gd' => '>2.0.28',
            'json' => '支持',
            'mysql' => '>5.x.x',
            'space' => '>50M',
            'upload' => '>2M',
            'version' => '>5.3.x',
            'pdo_mysql' => '支持',
        );
    }

    /**
     * 获取环境的最低配置要求
     *
     * @return array
     */
    private function _getLowestEnvironment() {
        return array(
            'os' => '不限制',
            'gd' => '2.0',
            'json' => '必须支持',
            'space' => '50M',
            'mysql' => '4.2',
            'upload' => '不限制',
            'version' => '5.3.7',
            'pdo_mysql' => '不限制',
        );
    }

    /**
     * 检查目录权限
     *
     * @return array
     */
    private function _checkFileRight() {

        $files_writeble[] = WEBPATH . 'cache/';
        $files_writeble[] = WEBPATH . SYS_UPLOAD_DIR. '/';
        $files_writeble[] = WEBPATH . 'config/site/';
        $files_writeble[] = WEBPATH . 'config/domain.php';
        $files_writeble[] = WEBPATH . 'config/system.php';
        $files_writeble[] = WEBPATH . 'config/database.php';

        $files_writeble = array_unique($files_writeble);
        sort($files_writeble);
        $writable = array();

        foreach ($files_writeble as $file) {
            $key = str_replace(WEBPATH, '', $file);
            $isWritable = $this->_checkWriteAble($file) ? true : false;
            if ($isWritable) {
                $flag = false;
                foreach ($writable as $k=>$v) {
                    if (0 === strpos($key, $k)) {
                        $flag = true;
                    }
                }
                $flag || $writable[$key] = $isWritable;
            } else {
                $writable[$key] = $isWritable;
            }
        }
        return $writable;
    }

    /**
     * 检查目录可写
     *
     * @param string $pathfile
     * @return boolean
     */
    private function _checkWriteAble($pathfile) {
        if (!$pathfile) {
            return false;
        }
        $isDir = in_array(substr($pathfile, -1), array('/', '\\')) ? true : false;
        if ($isDir) {
            if (is_dir($pathfile)) {
                mt_srand((double) microtime() * 1000000);
                $pathfile = $pathfile . 'dr_' . uniqid(mt_rand()) . '.tmp';
            } elseif (@mkdir($pathfile)) {
                return self::_checkWriteAble($pathfile);
            } else {
                return false;
            }
        }
        @chmod($pathfile, 0777);
        $fp = @fopen($pathfile, 'ab');
        if ($fp === false) {
            return false;
        }
        fclose($fp);
        $isDir && @unlink($pathfile);
        return true;
    }
}