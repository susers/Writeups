##  Title
game server

##  Tools
pwntools

##  Steps

- Step 0x00
  + name输入255字符
  + nobel输入255字符
  + 修改introduction
  + 栈溢出泄露函数地址找到对应的libc
  + 计算system地址
  + 再次栈溢出getshell

[exp脚本](/2018/RedHat%20CTF/Pwn/game%20server/files_for_writeups/exp_pwn2.py)