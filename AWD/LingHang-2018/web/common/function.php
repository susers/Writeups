<?php

function run_c($class){
    
    if ( !preg_match('/^\w+$/', $class) or (!file_exists(ROOTDRI."/lib/".$class.".php")) ){
        exit('hack');
    }else{
        // echo 1111;
        include "./lib/".$class.".php";
        return new $class;
    }
}


//
function run_a($obj,$action){
    if ( !preg_match('/^[\w\W].*$/', $action)){
        exit('hack');
    }else{
        eval('$obj->'.$action.'();');
    }
}



?>
