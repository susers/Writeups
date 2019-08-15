#!/usr/bin/env python

from Crypto.Util.number import *
from secret import exp, flag, nbit

assert exp & (exp + 1) == 0

def adlit(x):
    l = len(bin(x)[2:])
    return (2 ** l - 1) ^ x

def genadlit(nbit):
    while True:
        p = getPrime(nbit)
        q = adlit(p) + 31337
        if isPrime(q):
            return p, q

p, q = genadlit(nbit)
e, n = exp, p * q

c = pow(bytes_to_long(flag), e, n)

print 'n =', hex(n)
print 'c =', hex(c)