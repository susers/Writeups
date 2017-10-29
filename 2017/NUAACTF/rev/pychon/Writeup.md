##**Topic**
 pychon

## **Steps** 
文件头魔数，.pyc反编译

首先看后缀，是一个.pyc文件。由于pyc文件我们自己不好运行，于是可以直接丢当网上反编译一下:

[http://tool.lu/pyc/](http://tool.lu/pyc/)

![](files/rev0_0.png)

这种完全无法编译的原因，一般都是因为头部开始就解析错误。

```
RuntimeError: Bad magic number in .pyc file
```

然后上网查看这个文件格式的magic number到底是个啥，再stackoverflow上能够找到一个:

[http://www.jianshu.com/p/03d81eb9ac9b]
(http://www.jianshu.com/p/03d81eb9ac9b)

首先magic number的解释是

`The magic number comes from UNIX-type systems where the first few bytes of a file held a marker indicating the file type.`

也就是说，这个magic number是.pyc文件的标识符，如果开头不为那个固定的格式的话，就不能解析这个文件。
pyc开头固定四个字节为:

```
xx xx 0d 0a
```

xx依据版本号不同而不同，这里我们使用任何一个能够看到二进制的编辑器打开，能够看到:

```
16 0d 01 0a
```

显然有一位错误了，我们把其改成

```
16 0d 0d 0a 
```

然后运行，发现没有结果（当然，我这边用的是python3.5，如果使用了不同的版本的同学应该还是发生这个错误），然后再次丢当网上反编译一下，得到:

```
#!/usr/bin/env python
# encoding: utf-8
# 访问 http://tool.lu/pyc/ 查看更多信息
if __name__ == '__main__':
    str0 = [81,91,52,76,53,72,88,57,60,85,60,56,88,64,112,74]
    str1 = [1,2,3,4,5,6,7,8,9,10,9,8,7,6,5,4]
    ans = ''
    for (i, j) in zip(str0, str1):
ans += chr(i ^ j)
    
    flag = 'nuaactf{%s}' % ans

```

发现没有输出，自己执行一遍程序得到答案。