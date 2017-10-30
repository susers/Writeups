##**Topic**
web200

##**Steps**
打开页面发现页面跳转到 **?file=flag** ，并显示

```
nuaactf{this_is_the_fake_flag} 

Sorry, this is not the real flag.
```

这题虽然200分，入手点还是有很多的。

* 尝试修改查询字符串为flag.php(随便修改)，会报错

```
require_once(): Failed opening required 'flag.php.php'
```

说明原本查询的文件为flag.php，注意，require_once()，对于符合PHP语法的语句，会被包含进index.php并解析，不符合的将直接以文本形式显示。

* 或者删去查询字符串，执行

```
curl "http://localhost/www/index.php"
```

发现原页面内容为空，显然?file=flag影响了页面显示，也就是说flag这个文件是存在的，猜想flag文件有一部分没有显示出来。

* 最后用php://filter/read=convert.base64-encode/resource=flag即可显示文件内容，只需再base64解码即可。
