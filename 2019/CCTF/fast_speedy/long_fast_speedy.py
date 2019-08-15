#!/usr/bin/env python

import random
from Crypto.Util.number import *

def drift(R, B):
    n = len(B)
    ans, ini = R[-1], 0
    for i in B:
        ini ^= R[i-1]
    R = [ini] + R[:-1]
    return ans, R

flag = open('flag.png', 'r').read()
flag = bin(int(flag.encode('hex'), 16))[2:]

r = random.randint(7, 128)
s = random.randint(2, r)
R = [random.randint(0, 1) for _ in xrange(r)]
B = [i for i in xrange(s)]
random.shuffle(B)

A, enc = [], []
for i in range(len(flag)):
    ans, R = drift(R, B)
    A = A + [ans]
    enc = enc + [int(flag[i]) ^ ans]

enc = ''.join([str(b) for b in enc])
f = open('flag.png.enc', 'w')
f.write(long_to_bytes(int(enc, 2)))
f.close()