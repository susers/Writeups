<?php

if (!defined('BASEPATH')) exit('No direct script access allowed');

 
	
/**
 * 文件缓存
 */
	
class Dcache {

    /**
     * 缓存目录
     */
    public $cache_dir;

    /**
     * 构造函数,初始化变量
     */
    public function __construct() {
        $this->cache_dir = WEBPATH.'cache/data/'; // 设置缓存目录
    }

    /**
     * 设置缓存目录
     */
    public function set_dir($dir = null) {
        $this->cache_dir = $dir ? '' : WEBPATH . 'cache/data/'; // 设置缓存目录
    }

    /**
     * 分析缓存文件名
     *
     * @param 	string
     * @return 	string
     */
    private function parse_cache_file($file_name) {
        return $this->cache_dir.$file_name.'.cache.php';
    }

    /**
     * 设置缓存
     * 
     * @param string $key
     * @param string $value
     * @return boolean
     */
    public function set($key, $value) {

        if (!$key) {
            return false;
        }

        $cache_file = $this->parse_cache_file($key); // 分析缓存文件
        $value = (!is_array($value)) ? serialize($value) : serialize($value); // 分析缓存内容

        // 分析缓存目录
        if (!is_dir($this->cache_dir)) {
            dr_mkdirs($this->cache_dir, 0777);
        } else {
            if (!is_writeable($this->cache_dir)) {
                @chmod($this->cache_dir, 0777);
            }
        }

        return @file_put_contents($cache_file, $value, LOCK_EX) ? true : false;
    }
	
    /**
     * 获取一个已经缓存的变量
     * 
     * @param string $key
     * @return string
     */
    public function get($key) {

        if (!$key) {
            return false;
        }

        $cache_file = $this->parse_cache_file($key); // 分析缓存文件
        return is_file($cache_file) ? @unserialize(@file_get_contents($cache_file)) : false;
    }

    /**
     * 删除缓存
     * 
     * @param string $key
     * @return void
     */
    public function delete($key) {

        if (!$key) {
            return true;
        }

        $cache_file = $this->parse_cache_file($key);  // 分析缓存文件

        return is_file($cache_file) ? @unlink($cache_file) : true;
    }

}