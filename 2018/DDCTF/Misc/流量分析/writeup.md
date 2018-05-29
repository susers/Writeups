## 流量分析

## tOOLS
* wireshark

## STEPS
有用信息为最后几条SMTP流量
从中获取一张图片的base64码，在浏览器中转图片
对图片进行ocr识别得到字符（自动+人工）
补全RSA私钥格式:
-----BEGIN RSA PRIVATE KEY-----
XXXXX
-----END RSA PRIVATE KEY----- 
将私钥导入wireshark
decrypt最后几条ssl流量得到flag