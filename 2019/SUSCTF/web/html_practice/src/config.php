<?php
/**
 * Created by PhpStorm.
 * User: Machenike
 * Date: 19/3/23
 * Time: 9:19
 */
error_reporting(0);
function data_process($data){
    if (substr($data,0,2)==='0x'){
        $data = substr($data, 2);
        $data = hex2bin($data);
    }
    return $data;
}
function create_name($str){
    $salt='';
    for ($i = 0; $i < 10; $i++){
        $salt .= chr(mt_rand(33,126));
    }
    $str = "./html/".md5($str.$salt).".html";
    return $str;
}
class html{
    protected $html;
    public $username;
    function __construct($uname){
        $this->username = $uname;
        $this->html = create_name($uname);
    }
    /*function __wakeup(){
        $type = explode('.', $this->html);
        if (end($type)!=="html"){
            array_pop($type);
            $a = implode(".",$type).".html";
        }
        else{
            $a = implode(".",$type);
        }
        $this->html=$a;
    }*/
    function __destruct() {
        $this->run();
    }

    function run(){
        require_once($this->html);
    }
    function read(){
        return $this->html;
    }
}
