# WDCTF-finals-2017:2-1

## Steps

- Step 1

恢复png头为 `89 50 4e 47 0d 0a 1a 0a`

- Step 2

根据crc32值爆破图像宽度

```python
import os
import binascii
import struct


misc = open("misc4.png","rb").read()

for i in range(1024):
	data = misc[12:16] + struct.pack('>i',i)+ misc[20:29]
	crc32 = binascii.crc32(data) & 0xffffffff
	if crc32 == 0x932f8a6b:
		print i
```

得到709


![flag](files/misc4.png)

