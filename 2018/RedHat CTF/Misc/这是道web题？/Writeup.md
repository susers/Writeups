##这是道web题?

##Tools
- WebshellKill
- wireshark
- binwalk
##Steps
1. 使用webshellkill扫描，发现companytplfiles.php有问题
2. php文件中提示查看流量包中上传的文件。
3. 使用wireshark查看post包，在78466550-3fc1-11e8-9828-32001505e920.pcapng中post包传了一张图片到处对象保存为JPG文件
4. 使用binwalk查看jpg文件，发现隐藏着gif文件 使用dd if= of= skip= 指令提取的gif
5. 发现gif中有html编码，将gif分离，记录全部，解码得flag