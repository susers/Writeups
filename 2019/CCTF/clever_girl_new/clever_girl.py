#!/usr/bin/env python

import gmpy2
from fractions import Fraction
from secret import p, q, s, X, Y
from flag import flag

assert gmpy2.is_prime(p) * gmpy2.is_prime(q) > 0
assert Fraction(p, p+1) + Fraction(q+1, q) == Fraction(2*s - X, s + Y)
print 'Fraction(p, p+1) + Fraction(q+1, q) = Fraction(2*s - %s, s + %s)' % (X, Y)

n = p * q
c = pow(int(flag.encode('hex'), 16), 0x20002, n)
print 'n =', n
print 'c =', c
