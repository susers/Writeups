##  pcap

##  Tools

- example
- example

##  Steps


- Step 1

观察`png` 文件结构,发现大量`crc`报错

```xml
00000000  89 50 4e 47 0d 0a 1a 0a  00 00 00 0d 49 48 44 52  |.PNG........IHDR|
00000010  00 00 03 20 00 00 02 58  08 06 00 00 00 53 55 53  |... ...X.....SUS|
00000020  43 00 00 00 01 73 52 47  42 00 54 46 7b 30 00 00  |C....sRGB.TF{0..|
00000030  00 04 67 41 4d 41 00 00  b1 8f 36 46 62 36 00 00  |..gAMA....6Fb6..|
00000040  00 09 70 48 59 73 00 00  0e c4 00 00 0e c4 01 37  |..pHYs.........7|
00000050  35 38 33 00 00 ff a5 49  44 41 54 78 5e ec fd 67  |583....IDATx^..g|
00000060  db e4 d6 79 ad 8b ea ff  7f da 7b 2d 5b 99 6c c6  |...y......{-[.l.|
00000070  66 26 45 89 39 67 49 0e  b2 ad 40 91 0a 6c ca 96  |f&E.9gI...@..l..|
00000080  bd d2 de e7 3a ff a0 cf  1c 13 b8 51 03 a3 9e 09  |....:......Q....|
00000090  a0 ea 7d 9b 92 cf 5a 1f  ee 0b c0 33 f3 04 0a 18  |..}...Z....3....|
```

同时在CRC字段定位到flag关键字`SUSC{...`

猜测flag隐藏在各个数据块的CRC字段中,python脚本提取

```python
#!/usr/bin/env python
# -*- coding: utf-8 -*-
# @Date    : 2018-03-26 17:50:10
# @Author  : Xu (you@example.org)
# @Link    : https://xuccc.github.io/
# @Version : $Id$

import os
import string

class PNG:
    header = 0x8
    name = 0x4
    length = 0x4
    crc = 0x4

data = open('file.png','rb')
data.read(8)
flag = ''
while True:
    try:
        chunk_length = data.read(PNG.length)
        chunk_length = string.atoi(chunk_length.encode('hex'),16)
        chunk_type = data.read(PNG.name)
        chunk_data = data.read(chunk_length)
        chunk_crc = data.read(PNG.crc)
        print "{} {} ==> {}".format(data.tell(),chunk_type,chunk_crc.encode('hex'))
    except Exception as e:
        break
    else:
        flag += chunk_crc        
print flag


# xu@ubuntu  ~/susctf/misc/tmp  python flag.py 
# 33 IHDR ==> 53555343
# 46 sRGB ==> 54467b30
# 62 gAMA ==> 36466236
# 83 pHYs ==> 37353833
# 65540 IDAT ==> 66336337
# 131076 IDAT ==> 36616665
# 196612 IDAT ==> 36616665
# 262148 IDAT ==> 36646531
# 327684 IDAT ==> 65333465
# 393220 IDAT ==> 3962367d
# SUSCTF{06Fb67583f3c76afe6afe6de1e34e9b6}
```
