# php代码审计



## writeup

查看源码，发现会输出相应的变量，传一个GLOBAL进去即可输出当前所有定义的全局变量即可获取flag。
payload：
```
index.php?args=GLOBALS
```