<?php
/**
 * Created by PhpStorm.
 * User: y4ngyy
 * Date: 19-3-19
 * Time: 下午2:40
 */
class foo {
    public $filename;
    function printContent() {
        $content = file_get_contents($this->filename);
        echo $content;
    }
}
if ($_SERVER['HTTP_X_FORWARDED_FOR'] != '127.0.0.1') {
    echo 'Only Localhost can see';
    die();
} else if ($_SERVER['HTTP_USER_AGENT'] != 'SUS') {
    echo 'Browser is not SUS<br>';
    echo 'Please use SUS browser!';
    die();
}
show_source(__FILE__);


$a = null;
if (isset($_POST['foo'])) {
    $a = unserialize($_POST['foo']);
    if (!is_object($a)||get_class($a) != 'foo') {
        $a = new foo();
        $a->filename = "text.txt";
    }

} else {
    $a = new foo();
    $a->filename = "text.txt";
}
$a->printContent();
