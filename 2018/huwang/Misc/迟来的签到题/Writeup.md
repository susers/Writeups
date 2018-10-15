## Title

迟来的签到题

## Tools

- Python3

## Steps
1. base64解码
2. 发现前四位与'flag'亦或为同一个数字

## exp
```python3
import base64

x="AAoHAR1XICciX1IlXiBUVFFUIyRRJFRQVyUnVVMnUFcgIiNXXhs="
y=base64.b64decode(x.encode()).decode()
z=ord(y[0])^ord('f')

for c in y:
    print(chr(ord(c)^z),end='')
```
