# Title
infoGate
# Tools
	* firefox or chrome or burpsuite or curl, an vps
# Steps
	1. 使用Union注入整个流程，爆出admin的密码(由于没设计好，用万能密码也能登录 admin'or 1=1#(背锅..))
	2. 以admin账户登录，发现比guest多一个Uploads功能
	3. 在Upload中上传php文件内容反弹flag
	4. 拿到flag
