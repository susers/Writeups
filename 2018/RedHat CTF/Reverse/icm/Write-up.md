##  Title
icm

##  Tools
IDA Pro

##  Steps

- Step 0x00

首先从字符串常量 `You Are Right` 定位到关键函数 `main`

```
int __fastcall main(__int64 a1, char **a2, char **a3)
{
  int result; // eax
  char v4; // [rsp+20h] [rbp-50h]
  int v5; // [rsp+48h] [rbp-28h]
  __int16 v6; // [rsp+4Ch] [rbp-24h]
  unsigned __int64 v7; // [rsp+58h] [rbp-18h]

  v7 = __readfsqword(0x28u);
  memset(&v4, 0, 0x28uLL);
  v5 = 0;
  v6 = 0;
  if ( (unsigned int)sub_2047(&v4) != 42 )
    return -1;
  result = sub_1E84(&v4, 42);
  if ( result == 1 )
    result = puts("You Are Right!");
  return result;
}
```

其中 `sub_2047` 函数内部为 `strlen`，所以flag长度为42位

- step 0x01

可以看出，要输出 `You Are Right` 就需要让 `sub_1E84` 的返回值为1，下面分析这个函数

```
signed __int64 __fastcall sub_1E84(const char *a1, int a2)
{
  int v3; // [rsp+4h] [rbp-13Ch]
  int i; // [rsp+18h] [rbp-128h]
  int j; // [rsp+18h] [rbp-128h]
  int k; // [rsp+18h] [rbp-128h]
  char v7; // [rsp+20h] [rbp-120h]
  unsigned __int64 v8; // [rsp+128h] [rbp-18h]

  v8 = __readfsqword(0x28u);
  memset(&v7, 0, 0x100uLL);
  if ( a2 % 8 )
  {
    for ( i = 0; 8 - a2 % 8 > i; ++i )
      a1[a2 + i] = -35;
    a1[a2 + i] = 0;
  }
  v3 = strlen(a1);
  for ( j = 0; v3 / 8 > j; ++j )
    sub_1CFF(&a1[8 * j], 8LL);
  for ( k = 0; k < v3; ++k )
  {
    if ( ((119 - k) ^ (unsigned __int8)a1[k]) != byte_203040[k] )
      return 0xFFFFFFFFLL;
  }
  return 1LL;
}
```

可以看出，flag经过运算后与 `byte_203040` 进行比较，前半部分的运算是IDEA算法(国际数据加密算法)

第一个if中的循环生成密钥，所以我们可以在循环完成之后将a1数组dump出来

`sub_1CFF` 函数即为IDEA加密函数  [算法原理](http://www.elecfans.com/emb/600105.html)

所以我们只需要用脚本将 `byte_203040` 的数据异或即为IDEA加密后的密文，再对IDEA进行解密即可

[预处理](/2018/RedHat CTF/Reverse/icm/files_for_writeups/PreProcess.py)

[IDEA解密](/2018/RedHat CTF/Reverse/icm/files_for_writeups/Crack.c)