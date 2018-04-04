##  Title
Do u like py?

##  Tools
python

##  Steps

- Step 1
用uncompyle2或者[在线工具](https://tool.lu/pyc/)反编译pyc得到python代码，得到内容为base64的一段代码，base64解码得到

```
#获取明文并初始化变量
s = [ord(x) for x in raw_input("G1ve me fl4g:")];
a=[];
b=[];
c=[];
d=[];
mod = abs(False.__cmp__(1)) << 7;
g = [0];
l = len(s);
h = l + (4 - getattr(l, "__mod__")(4)) % 4;
d = h>>2;

#函数映射
e = [
	# e[0]: 输入按g[0]进行偏移
	lambda _, __: [s.__setitem__(i, (s.__getitem__(i)+g[0]*i+(i+2)*(i+1)) & ((1<<7)-1)) for i in range(0, l) if getattr([], "__class__").__name__[__].__eq__('l')],

	# e[1]: 无用代码
	(lambda __: [a.append(x>>3) for x in range(__, __.__mul__(__+1))])(2),

	# e[2]: 二进制栅栏
	lambda _, __: [([a.__setitem__(j, s[i+d*j]) == b.__setitem__(j, 0) for j in range(0, 4)].append([b.__setitem__(j, b.__getitem__(j) | ( a[(k+j)%4] & (3 << ((3-k)<<1)) ) ) for k in range(0, 4) for j in range(0, 4)]) is [s.__setitem__(i+d*j, b[j]) for j in range(0, 4)]) for i in range(0, d)],

	# e[3]: 执行b.extend(a), 参数1为无用参数
	lambda _, __: eval(__),

	# e[4]: 执行c.extend(a)，参数3为无用参数
	lambda _, __, ___: _.extend(__),

	# e[5]: 输入长度对齐至h
	lambda _, __: [s.__setitem__(i, i**2*h*_%mod) for i in range(_, h)],

	# e[6]: 检验原长度是否为h，不是就将l修改为h
	lambda _, __, ___: eval("exec _ = __") if _ != __ else eval("_.__add__(__.__sub__(_))"),

	# e[7]: 计算字符串特征值g[0]
	lambda _, __: [__.__setitem__(0, __[0]+(s[i]*(i+1))) for i in range(0, l)],

	# e[8]: 控制g[0]小于0x7f
	lambda _, __: _.__setitem__(0, g[0] & ((1<<(_[0] & 7))-1))
];

#函数执行，嵌套保证执行顺序
e[2]("SUS", e[0](e[8](g, e[7](e[6](l, h, e[5](l, e[4](c, a, e[3](e, "b.extend(a)")))), g)), 0));

#输出密文
print "".join([format(_, '02x') for _ in s])
```

- Step 2

上一步得到的代码是由 `lambda` 表达式和 `List comprehension` 递推列表混淆过的代码，分析后可以得出，加密共分为3个阶段

第一阶段对明文长度进行补齐，使其为4的倍数

第二阶段计算明文特征值tmod，范围为 tmod & 127，然后将明文逐位与特征值和位置进行计算，得到初步密文

第三阶段将初步密文分为4组，循环取每组第x位，将四个数的二进制进行偏移，然后放回原位，得到最终密文

本题在了解相关内容的前提下还需要一定的时间与耐心 ^_^

[未混淆代码](/2018/SUSCTF/Reverse/do_u_like_py/files_for_writeups/code.c)

[解密代码](/2018/SUSCTF/Reverse/do_u_like_py/files_for_writeups/Crack.py)