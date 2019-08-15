<?php

function alert($strs,$type)
{
    echo '<div class="alert alert-'.$type.'"><a class="close" data-dismiss="alert">&times;   </a><strong>提示:</strong><br/>'.$strs.'</div>';
}
function headers()
{
    $strs= <<<strs
<div class="header clearfix">
    <nav>
    <ul class="nav nav-pills pull-right">
        <li role="presentation"><a href="/">主页</a></li>
        <li role="presentation"><a href="post.php">投稿</a></li>
        <li role="presentation"><a href="commitbug.php">反馈</a></li>
        <li role="presentation"><a href="about.php">关于我</a></li>    
    </ul>
    </nav>
    <h3 class="text-muted">文章精选</h3>
</div>
strs;

    echo $strs;
}


function getJS(){
    $strs=<<<strs
<script src="/static/js/jquery.min.js"></script>
<script src="/static/js/bootstrap.js"></script>
<script src="/static/js/main.js"></script>
strs;
    echo $strs;
}


function waf($strX){
    $strX=str_replace("%28","（",$strX);
    $strX=str_replace("%29","）",$strX);
    $strX=str_replace("%22","”",$strX);
    $strX=str_replace("%27","’",$strX);
    $strX=str_replace("%2f%2f","  ",$strX);
    $strX=str_replace("%5c%5c","  ",$strX);
    $strX=str_ireplace("src","waf",$strX);

    $strX=str_replace("=","等于号",$strX);
    $strX=str_replace("%3d","等于号",$strX);


    $strX=str_replace("(","（",$strX);
    $strX=str_replace(")","）",$strX);
    $strX=str_replace("\"","”",$strX);
    $strX=str_replace("'","’",$strX);
    $strX=str_replace("//","waf",$strX);
    $strX=str_replace("\\","waf",$strX);
    return $strX;

}

function savepost($str){
    $str=waf($str);
    $str='<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />'.$str;
    $str='<meta http-equiv="content-security-policy" content="default-src \'self\'; script-src \'unsafe-inline\' \'unsafe-eval\'">'.$str;

    $urlx="./post/".md5(rand(1000000,9999999)).".html";
    $myfile = fopen($urlx, "w");
    fwrite($myfile, $str);
    fclose($myfile);
    $tampText = "<a class='article-click' src='$urlx'>点击查看</a>";
    return $tampText;
}

function savebug($str){
    $myfile = fopen("./submit_1bce5f764c10b1c3b7e2bf835cf31247/".md5($_SESSION['code_o']).".txt", "w");
    fwrite($myfile, $str);
    fclose($myfile);
}