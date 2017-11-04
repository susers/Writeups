# 不是管理员也能login

## writeup

### step1

点击说明与帮助可知userid的md5值以0e开头，查看源码可知
pwd为序列化的数组，数组的name与pwd值结尾0即可获取flag。