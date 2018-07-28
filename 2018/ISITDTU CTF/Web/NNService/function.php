<?php
function parseurl(){
    if(isset($_SERVER['PATH_INFO'])){
        $pathinfo = explode('/', trim($_SERVER['PATH_INFO'], "/"));
        $_GET['a'] = (!empty($pathinfo[0]) ? $pathinfo[0] : "index");
        array_shift($pathinfo);

        for($i = 0; $i<count($pathinfo); $i+=2){
            $_GET[$pathinfo[$i]] = $pathinfo[$i+1]==null?"":$pathinfo[$i+1];
        }
    }else{
        $_GET['a'] = (!empty($_GET['a']) ? $_GET['a']: "index");
    }
}
function filter($string){
    $preg="\\b(benchmark\\s*?\\(.*\\)|sleep\\s*?\\(.*\\)|load_file\\s*?\\()|UNION.+?SELECT\\s*(\\(.+\\)\\s*|@{1,2}.+?\\s*|\\s+?.+?|(`|\'|\").*?(`|\'|\")\\s*)|UPDATE\\s*(\\(.+\\)\\s*|@{1,2}.+?\\s*|\\s+?.+?|(`|\'|\").*?(`|\'|\")\\s*)SET|INSERT\\s+INTO.+?VALUES|(SELECT|DELETE)@{0,2}(\\(.+\\)|\\s+?.+?\\s+?|(`|\'|\").*?(`|\'|\"))FROM(\\{.+\\}|\\(.+\\)|\\s+?.+?|(`|\'|\").*?(`|\'|\"))|(CREATE|ALTER|DROP|TRUNCATE)\\s+(TABLE|DATABASE)";
    if(preg_match("/".$preg."/is",$string)){
        die('hacker');
    }
    return true;
}

function daddslashes($string)
{
    if (is_array($string)) {
        $keys = array_keys($string);
        foreach ($keys as $key) {
            $val = $string[$key];
            unset($string[$key]);
            $string[addslashes($key)] = daddslashes($val);
        }
    } else {
        $string =addslashes(trim($string));
    }
    return $string;
}

function quit($out){
    die("<script>alert('".$out."');history.go(-1);</script>");
}
function quit_and_refresh($out,$controller){
    die("<script>alert('".$out."');location.href='/index.php/".$controller."';</script>");
}

?>
