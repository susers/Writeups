## 安德门

## Tools
- Python
- C++

## Steps
之所以出100分是因为看懂了解密脚本十分简单
```python3
fin=open('flag.txt','rb')
a=fin.readline()[8:]
b=fin.readline()[8:]
for i in range(len(a)):print(chr(a[i]&b[i]),end='')
```
题目描述提示的是and门，也就是与运算。
文件需要用二进制读写，每个字节按位与就能得到flag。
因为有些同学不会python（我也很无奈啊），这里给出C++解法。
```C++
#include<iostream>
#include<fstream>
#include<cstring>
using namespace std;
int main()
{
	char a[100]={0};
	ifstream fin("flag.txt",ios::binary);
	fin.read(a,100);
	int l=strlen(a)/2;
	for(int i=8;i<l;i++)cout<<char(a[i]&a[l+i]);
	return 0;
}
```
注意：不要运行py脚本，否则会覆盖flag.txt，.DS_Store是mac压缩是生成的清单文件，没有用处