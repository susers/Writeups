<?php

setcookie("hint",base64_encode("please read recent papers about phar"));

include('./PicManager.php');
$manager=new PicManager('/var/www/html/sandbox/'.md5($_SERVER['HTTP_X_FORWARDED_FOR']));

if(isset($_GET['act'])){
    switch($_GET['act']){
        case 'upload':{
            if($_SERVER['REQUEST_METHOD']=='POST'){
                $manager->upload_pic();
            }
            break;
        }
        case 'get':{
            print $manager->get_pic($_GET['pic']);
            exit;
        }
        case 'clean':{
            $manager->clean();
            break;
        }
        default:{
            break;
        }

    }
}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf8" />
<title>GALLERY</title>
<link rel="stylesheet" type="text/css" href="demo.css" />
<link rel="stylesheet" href="jquery-ui.css" type="text/css" media="all" />
<link rel="stylesheet" type="text/css" href="fancybox/jquery.fancybox-1.2.6.css" />
<script
			  src="https://code.jquery.com/jquery-3.3.1.min.js"
			  integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8="
			  crossorigin="anonymous"></script>
<script
			  src="http://code.jquery.com/ui/1.12.0-rc.2/jquery-ui.min.js"
			  integrity="sha256-55Jz3pBCF8z9jBO1qQ7cIf0L+neuPTD1u7Ytzrp2dqo="
			  crossorigin="anonymous"></script>
<script type="text/javascript" src="fancybox/jquery.fancybox-1.2.6.pack.js"></script>
<script type="text/javascript" src="script.js"></script>
</head>
<body>
<div id="main">
	<h1>Gallery</h1>
    <h2>hello <?=$_SERVER['HTTP_X_FORWARDED_FOR'];?></h2>
	<div id="gallery">

        <?php

$stage_width=600;//放大后的图片宽度
$stage_height=400;//放大后的图片高度
$allowed_types=array('jpg','jpeg','gif','png');
$file_parts=array();
$ext='';
$title='';
$i=0;
$i=1;
$pics=$manager->pics();
foreach ($pics as $file)
{
	if($file=='.' || $file == '..') continue;
	$file_parts = explode('.',$file);
	$ext = strtolower(array_pop($file_parts));
//	$title = implode('.',$file_parts);
//	$title = htmlspecialchars($title);
	if(in_array($ext,$allowed_types))
	{
		$left=rand(0,$stage_width);
		$top=rand(0,400);
		$rot = rand(-40,40);
		if($top>$stage_height-130 && $left > $stage_width-230)
		{
			$top-=120+130;
			$left-=230;
		}
		/* 输出各个图片: */
		echo '
		<div id="pic-'.($i++).'" class="pic" style="top:'.$top.'px;left:'.$left.'px;background:url(\'http://'.$_SERVER['HTTP_HOST'].':'.$_SERVER["SERVER_PORT"].'/?act=get&pic='.$file.'\') no-repeat 50% 50%; -moz-transform:rotate('.$rot.'deg); -webkit-transform:rotate('.$rot.'deg);">
		<img src="http://'.$_SERVER['HTTP_HOST'].'/?act=get&pic='.$file.'" target="_blank"/>
		</div>';
	}
}
?>
    <div class="drop-box">
    </div>
	</div>
	<div class="clear"></div>
</div>
<div id="modal" title="上传图片">
	<form action="index.php?act=upload" enctype="multipart/form-data" method="post">
	<fieldset>
	<!--	<label for="url">文件：</label>-->
		<input type="file" name="file" id="url"  onfocus="this.select()" />
		<input type="submit" value="上传"/>
	</fieldset>
	</form>
</div>
</body>
</html>
