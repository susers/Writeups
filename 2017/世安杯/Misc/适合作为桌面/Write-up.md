##  Title

##  Tools

##  Steps

- Step 1

LSB 隐写得到二维码

- Step 2

扫描得到`pyc`文件的16进制

- Step 3

恢复为python脚本

```python
def flag():
    str = [
        102,
        108,
        97,
        103,
        123,
        51,
        56,
        97,
        53,
        55,
        48,
        51,
        50,
        48,
        56,
        53,
        52,
        52,
        49,
        101,
        55,
        125]
    flag = ''
    for i in str:
        flag += chr(i)
    
    print flag

flag()
```



