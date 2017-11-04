# phptrick

## writeup

### step1

查看源码，可发现php文件的部分源码：
```php
<!--
    	index.php
    	<?php     
		$flag='xxx';     
		extract($_GET);     
		if(isset($gift)){        
		    $content=trim(file_get_contents($flag));
		    if($gift==$content){ 
		       echo'flag';     }
		     else{       
		       echo'flag被加密了 再加密一次就得到flag了';}   
		     } 
		?>
        -->
```

### step2

构造请求

index.php?gift=mdzz&content=php://input, post data 内容为mdzz即可获取rot13加密后的flag，解密即可。
