#!/usr/bin/env python3
import random

flag="SUSCTF{example_flag}"
flag_a=[]
flag_b=[]
for c in flag:
    flag_bin=bin(ord(c))[2:].rjust(8,'0')
    a_bin=""
    b_bin=""
    for x in flag_bin:
        if x=='1':
            a_bin+='1'
            b_bin+='1'
        else:
            a=random.randint(0,1)
            b=random.randint(0,1-a)
            a_bin+=str(a)
            b_bin+=str(b)
    flag_a.append(int(a_bin,2))
    flag_b.append(int(b_bin,2))
with open("flag.txt",'wb') as f:
    f.write(b"flag_a: "+bytes(flag_a)+b'\n')
    f.write(b"flag_b: "+bytes(flag_b)+b'\n')
