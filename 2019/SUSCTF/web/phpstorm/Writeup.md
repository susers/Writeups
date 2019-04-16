# Title
phpStorm
# Tools
	* firefox or chrome or burpsuite or curl, an vps
# Steps
	1. index页面没用，但是有提示edit by phpstorm
	2. 找到phpstorm工作区泄露文件名有flag.php 还有真正问题.php
	3. 访问真正问题文件，发现只有localhost可以访问，x-forwarded-for 绕过，然后再User-Agent伪造浏览器。
	4. 看到源码，可以通过反序列化读文件
	5. 构造序列读取flag.php O:3:"foo":1:{s:8:"filename";s:8:"flag.php";}
	6. 查看源码看到flag