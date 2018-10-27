<?php

    class base{


        public $tp;
        
        function __construct(){
            

            
            $this->tp= new Smarty();
            $this->tp->left_delimiter='{';
            $this->tp->right_delimiter='}';
            $this->tp->template_dir='templates';   //html模板地址
            $this->tp->compile_dir='template_c';  //编译生成的文件
            $this->tp->cache_dir='cache'; //缓存
            $this->tp->caching=false;  //开启缓存
            $this->tp->cache_lifetime=120;//缓存时间
        }
    }

    

?>