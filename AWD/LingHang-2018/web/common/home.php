<?php

class home{
    
    private $method;
    private $args;
    function __construct($method, $args) {
        
      
        $this->method = $method;
        $this->args = $args;
    }

    function __destruct(){
        if (in_array($this->method, array("ping"))) {
            call_user_func_array(array($this, $this->method), $this->args);
        }
    } 

    function ping($host){
        system("ping -c 2 $host");
    }
    function waf($str){
        $str=str_replace(' ','',$str);
        return $str;
    }

    function __wakeup(){
        foreach($this->args as $k => $v) {
            $this->args[$k] = $this->waf(trim(mysql_escape_string($v)));
        }
    }   
}
     $a=@$_POST['a'];
    @unserialize($a);
    ?>
