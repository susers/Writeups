# WDCTF-2017-finals:3-2
## **【原理】**

## **【目的】**

## **【环境】**

## **【工具】**

## **【步骤】**

- Step 1

将key中内容,由`___`转化为数字

```python
5*((2//2+3+6-4%4)**((3%(3-1))+8+(3%3+5+7%2+6-(6//(5%3)))))+2*(((8/2)+3%2+7-(8//4))**(1*(5+5)+7+9%3))+8*(((9//2+8%2)+(7-1))**((3+7)+9-(6//2)))+7*((3+9-(6//3-7%2%1))**(5+5+5))+2*(2+9-(3//3-9%5%2))**(9-4+7)+(3+7)**(8%3%2+5+6)+(5-2)*((4//4-5%4%1)+9)**(5-(7//7+9%3)+6)+(5+(9%7)*2+1)**9+7*(((9%7)*2+7-(8//8))**7)+(8/2)*(((4-1+7)*(6+4))**3)+3*((2+9-1)**5)+3*(((3+7-6/3+2-9%5%2)*(3-1+8/2+9%5))**2)+(1//1)*(((8%3%2+5+5)%6)+7-1)**3+5*((6/(5%3))+7)*((9%7)*2+5+1)+3//3+9+9/3
```

- Step 2

反编译pyc文件得到py脚本

```

import random
import base64
from hashlib import sha1
strCipher = 'Xw6aM5fbiQOkkezmbdLC7Gbnj5siJJc5DpzkVjtdKPKT3A=='
key = 'xxxxxxx'

def crypt(data, key):
    x = 0
    box = range(256)
    for i in range(256):
        x = (x + box[i] + ord(key[i % len(key)])) % 256
        box[i] = box[x]
        box[x] = box[i]
    
    x = 0
    y = 0
    out = []
    for char in data:
        x = (x + 1) % 256
        y = (y + box[x]) % 256
        box[x] = box[y]
        box[y] = box[x]
        out.append(chr(ord(char) ^ box[(box[x] + box[y]) % 256]))
    
    return ''.join(out)


def encode(data, key, encode = base64.b64encode, salt_length = 16):
    salt = ''
    for n in range(salt_length):
        salt += chr(random.randrange(256))
    
    data = salt + crypt(data, sha1(key + salt).digest())
    if encode:
        data = encode(data)
    return data
```

 

## **【总结】**
