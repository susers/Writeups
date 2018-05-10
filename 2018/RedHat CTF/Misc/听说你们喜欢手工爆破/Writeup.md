##听说你们喜欢手工爆破
##Tools
- aapr
- aopr

##Steps
1. 下载附件，为一堆txt文件和一个加密的rar文件。打开txt文件里面均为相同的base64码VGgzcjMgMXMgbjAgZjFhZw== 解码后尝试不是rar文件的密码。
2. 用python提取所有txt文件名作为字典，使用aapr爆破rar密码，爆破成功。
3. 打开压缩包，里面为文件名为`情系海边之城`的doc文件，且文件被加密。 使用aopr爆破文件密码，爆破成功后打开文件。
4. 百度情系海边之城，得到提示曼彻斯特，解码doc文件中的门禁密码得到flag