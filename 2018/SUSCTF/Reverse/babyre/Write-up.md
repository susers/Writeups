##  Title
BabyRE

##  Tools
IDA Pro

##  Steps

- Step 0x00

查看二进制文件中的字符串可以发现upx字样，说明文件是经过upx加壳的，或者使用查壳工具同样可以。使用 `upx -d` 命令脱壳

- Step 0x01

```
int __cdecl main(int argc, const char **argv, const char **envp)
{
  __int64 v3; // rdx
  signed int v5; // [rsp+4h] [rbp-15Ch]
  signed int i; // [rsp+8h] [rbp-158h]
  signed int j; // [rsp+Ch] [rbp-154h]
  int v8[29]; // [rsp+10h] [rbp-150h]
  char v9[64]; // [rsp+E0h] [rbp-80h]
  char v10[56]; // [rsp+120h] [rbp-40h]
  unsigned __int64 v11; // [rsp+158h] [rbp-8h]

  v11 = __readfsqword(0x28u);
  memset(v8, 0, 0xC8uLL);
  v8[0] = -115;
  v8[1] = -40;
  v8[2] = -117;
  v8[3] = -56;
  v8[4] = -100;
  v8[5] = -38;
  v8[6] = -95;
  v8[7] = -12;
  v8[8] = -124;
  v8[9] = -4;
  v8[10] = -93;
  v8[11] = -110;
  v8[12] = -31;
  v8[13] = -66;
  v8[14] = -37;
  v8[15] = -70;
  v8[16] = -55;
  v8[17] = -80;
  v8[18] = -17;
  v8[19] = -101;
  v8[20] = -85;
  v8[21] = -12;
  v8[22] = -105;
  v8[23] = -27;
  v8[24] = -124;
  v8[25] = -25;
  v8[26] = -116;
  v8[27] = -14;
  v8[28] = -113;
  puts("Your flag?", argv, v8);
  _isoc99_scanf((unsigned __int64)"%s");
  for ( i = 0; i <= 49; ++i )
  {
    if ( !v9[i] )
    {
      v5 = i;
      break;
    }
    if ( i )
    {
      v3 = (unsigned __int8)v9[i] ^ (unsigned int)(unsigned __int8)v10[i - 1];
      v10[i] = v9[i] ^ v10[i - 1];
    }
    else
    {
      v10[0] = v9[0] ^ 0xDE;
    }
  }
  for ( j = 0; j < v5; ++j )
  {
    v3 = (unsigned int)v8[j];
    if ( (_DWORD)v3 != v10[j] )
    {
      puts("You should try harder.", v9, v3);
      return 0;
    }
  }
  puts("Congratz.", v9, v3);
  return 0;
}
```

上面代码中将v8的变量类型手动修正为int[29]，更容易理解，可以看出，最终的比较部分为v8与v10，v8是程序中固定的密文，v10是明文经过运算后的内容，运算过程为v10[i] = v9[i] ^ v10[i-1]，当i为0时，与0xDE进行异或，由于异或运算是可逆的，解密脚本并不难写。

[题目源码](/2018/SUSCTF/Reverse/babyre/files_for_writeups/CrackMe.c)

[解密脚本](/2018/SUSCTF/Reverse/babyre/files_for_writeups/Crack.c)