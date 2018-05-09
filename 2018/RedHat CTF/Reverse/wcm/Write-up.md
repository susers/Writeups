##  Title
wcm

##  Tools
IDA Pro

##  Steps

- Step 0x00

第一步还是检测字符串长度

```
int __cdecl main(int argc, const char **argv, const char **envp)
{
  FILE *v3; // eax
  char v5; // [esp+1h] [ebp-55h]
  char Buf; // [esp+2h] [ebp-54h]
  char Dst; // [esp+3h] [ebp-53h]
  char v8; // [esp+2Ch] [ebp-2Ah]
  char v9; // [esp+2Dh] [ebp-29h]

  Buf = 0;
  memset(&Dst, 0, 0x4Fu);
  puts("Input You Flag");
  v3 = _iob_func();
  fgets(&Buf, 44, v3);
  if ( v8 != 10 )
    return -1;
  v9 = 0;
  *(&v5 + strlen(&Buf)) = 0;
  if ( strlen(&Buf) != 42 )
    return -1;
  if ( sub_4013B0(&Buf, 42) == 1 )
    printf("You are Right!\n");
  return 0;
}
```

- step 0x01

然后分析 `sub_4013B0`

```
signed int __fastcall sub_4013B0(const char *a1, int a2)
{
  int v2; // ebx
  signed int v3; // esi
  const char *v4; // ecx
  int v5; // esi
  int v6; // ebx
  unsigned __int8 *v7; // edi
  int v8; // ecx
  signed int v9; // eax
  int v11; // [esp+Ch] [ebp-90h]
  const char *v12; // [esp+10h] [ebp-8Ch]
  char v13; // [esp+18h] [ebp-84h]

  v12 = a1;
  v11 = a2;
  v2 = a2 % 16;
  srand(0x2872DD1Bu);
  v3 = 0;
  do
    byte_403370[v3++] = rand();
  while ( v3 < 16 );
  if ( v2 && 16 - v2 > 0 )
    memset((void *)&v12[v11], 0xFFu, 16 - v2);
  v4 = v12;
  v5 = strlen(v12);
  v6 = v5 / 16;
  if ( v5 / 16 > 0 )
  {
    v7 = (unsigned __int8 *)v12;
    do
    {
      sub_401000(&v13);
      sub_401190(v8, v7, v7);
      v7 += 16;
      --v6;
    }
    while ( v6 );
    v4 = v12;
  }
  if ( v5 <= 0 )
    return 1;
  v9 = 51;
  while ( (v9 ^ (unsigned __int8)v4[v9 - 51]) == byte_402140[v9 - 51] )
  {
    ++v9;
    v4 = v12;
    if ( v9 - 51 >= v5 )
      return 1;
  }
  return -1;
}
```

`byte_403370` 是由伪随机产生的，可以用同样的种子生成伪随机，或者动态调试dump出来，这个就是下面算法用到的密钥

后面的算法是 `SM4`  [算法原理](https://blog.csdn.net/archimekai/article/details/53095993)

验证数据的提取和icm相同，dump出来之后通过异或还原为SM4算法的密文

[解密脚本](/2018/RedHat%20CTF/Reverse/wcm/files_for_writeups/exp.py)