##Not Only Wireshark
##Tools
- Wireshark
- tshark

##Steps
1. 下载流量包，过滤查看http，发现请求发送的name参数很可疑

2. 使用tshark指令或者python脚本提取name参数。

3. 提取出的参数头为 123404B0304 想到zip头为504B0304，修改文件头保存为zip文件

4. zip文件需要密码，在流量包中搜寻key，password等关键字，发现一条流量中key=?id=1128%2

5. 输入密码，解得flag
