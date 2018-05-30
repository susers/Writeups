<?php

/**
 * 生成配置文件
 */

class Dconfig {

    private $note;
    private $file;
    private $space;
    private $header;

    /**
     * 生成配置文件
     */
    public function __construct() {
        $this->note	 = '';
        $this->space = 32;
    }

    /**
     * 配置文件
     *
     * @param	string	$file	文件绝对地址
     * @return	object
     */
    public function file($file) {
        $this->file = $file;
        $this->header = '<?php'.PHP_EOL.PHP_EOL.
            'if (!defined(\'BASEPATH\')) exit(\'No direct script access allowed\');'.PHP_EOL.PHP_EOL.
            '/**'.PHP_EOL.
            ' * FineCMS'.PHP_EOL.
            ' */'.PHP_EOL.PHP_EOL
        ;
        return $this;
    }

    /**
     * 备注信息
     *
     * @param	string	$note	备注
     * @return	object
     */
    public function note($note) {
        $this->note = '/**'.PHP_EOL.
            ' * '.$note.PHP_EOL.
            ' */'.PHP_EOL.PHP_EOL
        ;
        return $this;
    }

    /**
     * 空格数量
     *
     * @param	int	$num	变量名称与值间的空格数量
     * @return	object
     */
    public function space($num) {
        $this->space = $num;
        return $this;
    }

    public function to_header() {
        return $this->header.$this->note;
    }

    /**
     * 生成require一维数组文件
     *
     * @param	array	$var	变量标识	array('变量名称' => '备注信息'), ...
     * @param	array	$data	对应值数组	array('变量名称' => '变量值'), ... 为空时直接生成$var
     * @return	int
     */
    public function to_require_one($var, $data = NULL, $all = 0) {



        $body = $this->header.$this->note.'return array('.PHP_EOL.PHP_EOL;

        if ($var) {

            if ($data) {
                if ($all) {
                    foreach ($data as $name => $v) {
                        if (is_array($data[$name])) {
                            continue;
                        }
                        $note = isset($var[$name]) ? $var[$name] : '';
                        $body.= '	\''.$name.'\''.$this->_space($name).'=> '.$this->_format_value($data[$name]).', //'.$note.PHP_EOL;
                    }
                } else {
                    foreach ($var as $name => $note) {
                        if (is_array($data[$name])) {
                            continue;
                        }
                        $body.= '	\''.$name.'\''.$this->_space($name).'=> '.$this->_format_value($data[$name]).', //'.$note.PHP_EOL;
                    }
                }
            } else {
                foreach ($var as $name => $val) {
                    if (is_array($val)) {
                        continue;
                    }
                    $body.= '	\''.$name.'\''.$this->_space($name).'=> '.$this->_format_value($val).','.PHP_EOL;
                }
            }
        }

        $body.= PHP_EOL.');';
        if (!is_dir(dirname($this->file))) {
            dr_mkdirs(dirname($this->file));
        }

        return @file_put_contents($this->file, $body, LOCK_EX);
    }


    /**
     * 生成require N维数组文件
     *
     * @param	array	data
     * @return	int
     */
    public function to_require($data) {
        $body = $this->header.$this->note.'return ';
        $body .= str_replace(array('  ', ' 
    '), array('    ', ' '), var_export($data, TRUE));
        $body .= ';';
        if (!is_dir(dirname($this->file))) dr_mkdirs(dirname($this->file));
        return @file_put_contents($this->file, $body, LOCK_EX);
    }

    /**
     * 补空格
     *
     * @param	string	$name	变量名称
     * @return	string
     */
    private function _space($name) {
        $len = strlen($name) + 2;
        $cha = $this->space - $len;
        $str = '';
        for ($i = 0; $i < $cha; $i ++) $str .= ' ';
        return $str;
    }

    private function _format_value($value) {
        return is_numeric($value) && strlen($value) <= 10 ? $value : '\''.str_replace(array('\'', '\\'), '', $value).'\'';
    }
}