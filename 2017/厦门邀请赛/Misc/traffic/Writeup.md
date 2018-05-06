##  traffic

##  Tools


##  Steps

- Step 1

比较难以捉摸,直接说步骤,要先发现开始`icmp`包中大量充斥的`a`字符串及`heiheiehei!`的提示

- Step 2

数据隐藏在`icmp`包的长度中,提取下数据

```sh
$ tshark -r 2.pcapng -Y 'icmp && ip.src == 10.6.6.143' -T fields -e frame.len|tr '\n' ','
144,150,139,145,165,91,109,151,122,113,106,119,93,167,
```


- Step 3

每个减42！

```sh
In [1]: flag = ''

In [2]: for i in (144,150,139,145,165,91,109,151,122,113,106,119,93,167):
   ...:     flag += chr(i-42)
   ...: 

In [3]: print flag
flag{1CmPG@M3}
```


XXX


