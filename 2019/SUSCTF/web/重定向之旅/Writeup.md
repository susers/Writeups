## 重定向之旅

## Tools
- 浏览器

## Steps
每一关自带hint（点击“关于”出现）和背景音乐。建议使用谷歌内核浏览器是为了保证正确重定向（虽然我对IE和火狐做了兼容），其实大部分国产浏览器的极速模式就是谷歌内核。。。
- 第一关：网页源代码
	- hint：源代码的秘密
	- 使用view-source(Ctrl+U)可以看到`<!--<?php $part1="3oI";?>-->`
	- 本关过场动画使用`<img>`标签实现
	- 本关使用html文件的meta实现重定向
- 第二关：响应头
	- hint：你摸得着头脑吗？
	- 查看响应头可以看到`$part2: rEdirEct`
	- 本关过场动画使用PHP缓冲流实现
	- 本关使用PHP的响应头实现重定向
	- 本关检查Referer，不是来自index-ein.html的请求会被重定向至404页面
- 第三关：JS
	- hint：AAencode
	- 查看网页源码发现引用JS文件index-ne.js，打开发现是一堆颜文字（将编码改为unicode方可正常显示）
	- 根据提示找一个AAencode在线解密网站，比如[http://ctf.ssleye.com/aaencode.html](http://ctf.ssleye.com/aaencode.html)，解密出来为
	```JavaScript
	leave=function ()
	{console.log("$part3:4fun");
	location.href='flag.php';
	location.href='no_flag.html';}
	```
	- 打开flag.php发现`echo "SUSCTF{".$part1."_".$part2."_".$part3."}";`，将三部分flag按格式拼接即得最终flag
	- 本关过场动画使用JavaScrpt＋CSS实现
	- 本关使用JS修改location实现重定向
	- 本关的第二个重定向会覆盖第一个重定向
- 隐藏关卡
	- 查看历史记录发现访问了index-ein.html、index-dos.php、index-trois.aspx、index-ne.js，似乎有某种规律
	- 查看休息室中那首歌的歌词发现EIN(一)、DOS(二)、TROIS(三)、NE(四)、FEM(五)、LIU(六)，分别为德语、西班牙语、法语、韩语、瑞典语、汉语
	- 访问index-fem.html发现红包，访问index-liu.html发现啥都没有
