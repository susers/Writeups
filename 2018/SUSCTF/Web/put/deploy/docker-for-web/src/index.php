<?php
/**
 * Created by PhpStorm.
 * User: image
 * Date: 18-3-17
 * Time: 下午2:27
 */
$flag='Susctf{request_in_put_method}';
if($_SERVER['REQUEST_METHOD']=='PUT'){

    $data=file_get_contents('php://input');
    if($data==='message'){
        die(base64_encode($flag));
    }
    else{
        die("PUT me message!");
    }
}
?>
put me a message then you can get the flag
