<?php
error_reporting(0);
highlight_file("file_contrary.php");
$filename=$_GET['filename'];
if (preg_match("/\bphar\b/A", $filename)) {
    echo "stop hacking!\n";
}
else {
    class comrare
    {
        public $haha = 'ciscn2019';

        function __wakeup()
        {
            eval($this->haha);
        }

    }
    imagecreatefromjpeg($_GET['filename']);
}
?>
