##  Title
advanced ecc

##  Tools

##  Steps

这题主要漏洞点在

```
assert (abs(r[0] - r[1]) <= (1 << 20))
assert (abs(r[0] - r[2]) <= (1 << 20))
assert (abs(r[1] - r[2]) <= (1 << 20))
```

那么我们可以利用返回的level1的C2和level2的C2与G爆破出r[0]-r[1]；
再利用level1的C1和level2的C1与K求出M就可以解密了

[脚本](/2018/RedHat%20CTF/Crypto/advanced%20ecc/files_for_writeups/exp.py)