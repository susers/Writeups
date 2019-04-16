## SUS

## Tools
- pkcrack
- ARCHPR
- github

## Steps
- 解法1：爆破（为了防止做不出来留的方法）
为了考验爆破者的耐心，密码为11位纯数字，选对模式大约需要爆破10分钟
- 解法2：明文攻击
	- 实际中明文一般不与加密文件一同出现，所以这题的明文需要自己上网找。
	比如http://66.39.39.113/upa_publications/jus/2009may/JUS_Bangor_May2009.pdf
	（注意比较文件大小，ACM的那篇明显与其他网站的不一样大）
	- 之后尝试压缩（注意：压缩方式相同时，一般明文压缩比密文压缩要小12字节）
	`zipinfo -v SUS.zip`可以看到该压缩包是windows系统下压缩的，经尝试winrar默认压缩方式相同。
	- Windows:ARCHPR选择明文攻击模式（注意恢复密钥后不需要尝试恢复密码可直接停止得到解密的压缩包）
	Linux:`pkcrack -C SUS.zip -c JUS_Bangor_May2009.pdf -P JUS_Bangor_May2009.zip -p JUS_Bangor_May2009.pdf -d out.zip -a`
- 解法3：社工
	- 根据README.md推测这是一个代码托管平台自动生成的文件。
	- Github上搜索https://github.com/search?q=System+Usability+Scale+(SUS)
	- 发现[https://github.com/zhengbili/System-Usability-Scale](https://github.com/zhengbili/System-Usability-Scale)，[https://github.com/zhengbili/System-Usability-Scale/blob/master/flag.txt](https://github.com/zhengbili/System-Usability-Scale/blob/master/flag.txt)中的flag为SUSCTF{example_f1ag}
	- 真正的flag在历史提交记录里，查看[https://github.com/zhengbili/System-Usability-Scale/commit/0173cdad9d69b2015db8cf0b255f6d1309915ee6#diff-159df48875627e2f7f66dae584c5e3a5](https://github.com/zhengbili/System-Usability-Scale/commit/0173cdad9d69b2015db8cf0b255f6d1309915ee6#diff-159df48875627e2f7f66dae584c5e3a5)即得。